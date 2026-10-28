<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\FcmToken;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The signed-in user's notification inbox and push-token registry.
 * Response shapes match lib/types/notifications.ts in crg-portal.
 */
class NotificationController extends Controller
{
    public function index(Request $request): mixed
    {
        $userId = $request->user()->id;
        $unreadOnly = in_array($request->input('unreadOnly') ?? 'false', ['true', '1'], true);
        $type = $request->input('type');

        $query = AppNotification::where('user_id', $userId)
            ->when($unreadOnly, fn ($q) => $q->whereNull('read_at'))
            ->when(!empty($type), fn ($q) => $q->where('type', $type))
            ->latest('created_at')
            ->latest('id');

        if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
            $limit = (int) ($request->input('limit') ?? 20);
            $page = $query->paginate($limit, ['*'], 'page', (int) ($request->input('page') ?? 1));

            return response()->json([
                'success' => true,
                'data' => collect($page->items())->map(fn (AppNotification $n) => $this->present($n))->values(),
                'unreadCount' => $this->unread($userId),
                'pagination' => [
                    'page' => $page->currentPage(),
                    'limit' => $page->perPage(),
                    'total' => $page->total(),
                    'totalPages' => max(1, $page->lastPage()),
                ],
            ]);
        }

        $notifications = $query->paginate(20);
        $unreadCount = $this->unread($userId);
        $activeBuses = \App\Models\Bus::where('status', 'active')->count();
        $users = \App\Models\User::orderBy('name')->get();
        $drivers = \App\Models\Driver::with('user')->get();

        return view('notifications', compact('notifications', 'unreadCount', 'activeBuses', 'users', 'drivers'));
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'unreadCount' => $this->unread($request->user()->id),
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = AppNotification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        abort_unless($notification, 404);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['success' => true, 'data' => $this->present($notification)]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $updated = AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        \App\Services\CacheService::invalidateGroup(\App\Services\CacheService::GROUP_NOTIFICATIONS);

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    /** POST /auth/fcm-token — registers (or re-assigns) this device's push token */
    public function registerToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fcmToken' => ['required', 'string', 'max:512'],
            'platform' => ['nullable', 'in:WEB,ANDROID,IOS'],
        ]);

        FcmToken::updateOrCreate(
            ['token' => $data['fcmToken']],
            ['user_id' => $request->user()->id, 'platform' => $data['platform'] ?? 'WEB']
        );

        return response()->json(['success' => true, 'message' => 'Device registered.']);
    }

    /** DELETE /notifications/token — unregisters this device (on logout) */
    public function removeToken(Request $request): JsonResponse
    {
        $data = $request->validate(['fcmToken' => ['required', 'string', 'max:512']]);

        FcmToken::where('token', $data['fcmToken'])
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Device removed.']);
    }

    public function tokens(Request $request): JsonResponse
    {
        $tokens = FcmToken::where('user_id', $request->user()->id)->latest()->get();
        $ready = $this->pushReady();

        return response()->json([
            'success' => true,
            'message' => $ready
                ? 'Push notifications are configured.'
                : 'Push delivery is not configured on the server yet; notifications appear in the app only.',
            'fcmReady' => $ready,
            'count' => $tokens->count(),
            'tokens' => $tokens->map(fn (FcmToken $t) => [
                'id' => (string) $t->id,
                'platform' => $t->platform,
                'fcmToken' => strlen($t->token) > 16
                    ? substr($t->token, 0, 10) . '…' . substr($t->token, -4)
                    : '…',
                'createdAt' => $t->created_at?->toIso8601String(),
                'updatedAt' => $t->updated_at?->toIso8601String(),
            ])->values(),
        ]);
    }

    /**
     * POST /notifications/broadcast or /api/notifications/broadcast
     * Create role-wise or user-specific notification by Admin
     */
    public function broadcast(Request $request): mixed
    {
        $validated = $request->validate([
            'target_type' => ['required', 'string', 'in:role,user,all'],
            'role' => ['nullable', 'string', 'in:all,driver,admin,super_admin,staff'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'type' => ['required', 'string', 'max:60'],
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
            'priority' => ['nullable', 'string', 'in:normal,high,urgent'],
            'link' => ['nullable', 'string', 'max:255'],
        ]);

        $userIds = [];

        if ($validated['target_type'] === 'user' && !empty($validated['user_id'])) {
            $userIds = [(int) $validated['user_id']];
        } elseif ($validated['target_type'] === 'role') {
            $role = $validated['role'] ?? 'all';
            if ($role === 'all') {
                $userIds = \App\Models\User::pluck('id')->all();
            } elseif ($role === 'driver') {
                $driverUserIds = \App\Models\User::where('role', 'driver')->pluck('id')->all();
                $mappedDriverIds = \App\Models\Driver::whereNotNull('user_id')->pluck('user_id')->all();
                $userIds = array_values(array_unique(array_merge($driverUserIds, $mappedDriverIds)));
            } elseif ($role === 'admin' || $role === 'super_admin') {
                $userIds = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
            } else {
                $userIds = \App\Models\User::where('role', $role)->pluck('id')->all();
            }
        } else {
            // 'all'
            $userIds = \App\Models\User::pluck('id')->all();
        }

        if (empty($userIds)) {
            $userIds = \App\Models\User::pluck('id')->all();
        }

        $sentCount = Notifier::send(
            $userIds,
            $validated['type'],
            $validated['title'],
            $validated['body'],
            [
                'priority' => $validated['priority'] ?? 'normal',
                'sender' => $request->user()?->name ?? 'Admin',
                'broadcast_at' => now()->toIso8601String(),
            ],
            $validated['link'] ?? null
        );

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => "Notification broadcasted successfully to {$sentCount} recipient(s).",
                'recipient_count' => $sentCount,
            ]);
        }

        return redirect()->route('notifications')->with('success', "Notification broadcasted successfully to {$sentCount} recipient(s).");
    }

    /** POST /notifications/test — puts a test notification in your own inbox */
    public function test(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
        ]);

        Notifier::send([$request->user()->id], 'TEST', $data['title'], $data['body'], [], '/dashboard/notifications');

        return response()->json([
            'success' => true,
            'message' => $this->pushReady()
                ? 'Test notification sent.'
                : 'Test notification added to your inbox. Push delivery is not configured on the server.',
        ]);
    }

    private function unread(int $userId): int
    {
        return AppNotification::where('user_id', $userId)->whereNull('read_at')->count();
    }

    /** Push needs Firebase service-account credentials on the server */
    private function pushReady(): bool
    {
        $path = env('FIREBASE_CREDENTIALS');

        return is_string($path) && $path !== '' && is_file($path);
    }

    private function present(AppNotification $n): array
    {
        return [
            'id' => (string) $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'body' => $n->body,
            'data' => (object) ($n->data ?? []),
            'link' => $n->link,
            'isRead' => $n->read_at !== null,
            // Display string, e.g. "04-Aug-2026, 05:12 pm"
            'datetime' => $n->created_at?->format('d-M-Y, h:i a'),
            'createdAt' => $n->created_at?->toIso8601String(),
        ];
    }
}

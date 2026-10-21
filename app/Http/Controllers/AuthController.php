<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\UserPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'recruiter',
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Registration submitted. An administrator must approve your account before you can log in.',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        // Accept email, username, or driver badge_id
        $loginIdentifier = trim((string) (
            $request->input('email')
            ?? $request->input('username')
            ?? $request->input('badge_id')
            ?? $request->input('badgeNumber')
            ?? ''
        ));

        $password = (string) $request->input('password', '');

        if ($loginIdentifier === '' || $password === '') {
            $errors = [];
            if ($loginIdentifier === '') {
                $errors['email'] = ['The email field is required.'];
            }
            if ($password === '') {
                $errors['password'] = ['The password field is required.'];
            }

            return response()->json([
                'message' => 'Both email and password are required to log in.',
                'errors' => $errors,
            ], 422);
        }

        // Find user by email or by associated driver badge_id or phone
        $user = User::where('email', $loginIdentifier)->first();
        if (!$user) {
            $user = User::whereHas('driver', function ($q) use ($loginIdentifier): void {
                $q->where('badge_id', $loginIdentifier)->orWhere('phone', $loginIdentifier);
            })->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect. Please verify your email and password.',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ], 401);
        }

        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'Your account is pending administrator approval.',
                'errors' => [
                    'email' => ['Your account is pending administrator approval.'],
                ],
            ], 403);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'This account is inactive. Please contact your fleet administrator.',
                'errors' => [
                    'email' => ['This account is inactive.'],
                ],
            ], 403);
        }

        if ($reason = $this->driverLoginBlockReason($user)) {
            return response()->json([
                'message' => $reason,
                'errors' => [
                    'email' => [$reason],
                ],
            ], 403);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return response()->json($this->tokenResponse($user));
    }

    public function refresh(Request $request): JsonResponse
    {
        $data = $request->validate([
            'refreshToken' => ['required', 'string'],
        ]);

        $token = PersonalAccessToken::findToken($data['refreshToken']);

        if (!$token || !str_starts_with($token->name, 'refresh-')) {
            return response()->json(['message' => 'Invalid refresh token.'], 401);
        }

        $user = $token->tokenable;

        if ($user->status !== 'active') {
            $token->delete();

            return response()->json(['message' => 'This account is not approved.'], 403);
        }

        if ($reason = $this->driverLoginBlockReason($user)) {
            $token->delete();

            return response()->json(['message' => $reason], 403);
        }

        $token->delete();

        return response()->json($this->tokenResponse($user));
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            // Revoke the token that authenticated this request.
            $user->currentAccessToken()?->delete();

            // Revoke every refresh token so the session cannot be silently renewed.
            $user->tokens()->where('name', 'like', 'refresh-%')->delete();
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Explain why a user may not sign in to the driver API.
     *
     * Returns null when the user is allowed to sign in. Only drivers are
     * permitted, and a driver must already have a vehicle assigned to them.
     */
    private function driverLoginBlockReason(User $user): ?string
    {
        if (!in_array($user->role, ['driver', 'admin', 'super_admin'], true)) {
            return 'Only drivers and fleet administrators can sign in to this app.';
        }

        return null;
    }

    private function tokenResponse(User $user): array
    {
        $user->tokens()->where('name', 'like', 'refresh-%')->delete();

        return [
            'user' => $this->formatUser($user),
            'accessToken' => $user->createToken('access-' . $user->id)->plainTextToken,
            'refreshToken' => $user->createToken('refresh-' . $user->id)->plainTextToken,
        ];
    }

    private function formatUser(User $user): array
    {
        return UserPresenter::format($user);
    }

}
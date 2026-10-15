<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status === 'pending') {
            throw ValidationException::withMessages([
                'email' => ['Your account is pending administrator approval.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['This account is inactive.'],
            ]);
        }

        if ($reason = $this->driverLoginBlockReason($user)) {
            throw ValidationException::withMessages([
                'email' => [$reason],
            ]);
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
        $request->user()?->currentAccessToken()?->delete();

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
        if ($user->role !== 'driver') {
            return 'Only drivers can sign in to this app. Please use the fleet web console instead.';
        }

        $driver = $user->driver;

        if (!$driver || !$driver->bus()->exists()) {
            return 'No vehicle is assigned to your account yet. Please contact dispatch to assign a vehicle before signing in.';
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
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'last_login_at' => $user->last_login_at?->toIso8601String(),
        ];

        if ($user->role !== 'driver') {
            return $data;
        }

        $driver = $user->driver;

        if (!$driver) {
            return $data;
        }

        $data['driver'] = [
            'id' => $driver->id,
            'user_id' => $driver->user_id,
            'badge_id' => $driver->badge_id,
            'phone' => $driver->phone,
            'license_number' => $driver->license_number,
            'license_type' => $driver->license_type,
            'license_expiry' => $driver->license_expiry?->format('Y-m-d'),
            'duty_status' => $driver->duty_status,
            'experience_years' => $driver->experience_years,
            'notes' => $driver->notes,
        ];

        $bus = $driver->bus;

        $data['bus'] = $bus ? [
            'id' => $bus->id,
            'bus_number' => $bus->bus_number,
            'plate' => $bus->plate,
            'model' => $bus->model,
            'status' => $bus->status,
            'odometer' => $bus->odometer,
            'fuel_month_liters' => $bus->fuel_month_liters,
            'fuel_cost' => $bus->fuel_cost,
            'km_per_liter' => $bus->km_per_liter,
            'next_service_km' => $bus->next_service_km,
            'route' => $bus->route,
        ] : null;

        return $data;
    }
}
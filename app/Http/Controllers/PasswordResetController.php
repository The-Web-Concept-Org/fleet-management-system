<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\DriverMailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Driver password recovery for the mobile app.
 *
 * A 6-digit one-time code is emailed to the driver. The code is stored hashed
 * in the standard password_reset_tokens table and must be exchanged for a new
 * password within the configured expiry window.
 */
class PasswordResetController extends Controller
{
    /**
     * Email a one-time reset code to a driver account.
     */
    public function forgot(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $expiresInMinutes = $this->expiryMinutes();
        $user = User::where('email', $data['email'])->first();

        // Only driver accounts with a driver profile can reset through the app.
        // The response is identical either way so the endpoint cannot be used
        // to discover which emails belong to drivers.
        if ($user && $user->role === 'driver' && $user->driver) {
            $code = (string) random_int(100000, 999999);

            $this->tokenTable()->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($code), 'created_at' => now()],
            );

            DriverMailer::sendPasswordResetCode($user->driver, $code, $expiresInMinutes);
        }

        return response()->json([
            'message' => 'If that email belongs to a driver account, a password reset code has been sent.',
            'expires_in_minutes' => $expiresInMinutes,
        ]);
    }

    /**
     * Exchange a valid reset code for a new password.
     */
    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $record = $this->tokenTable()->where('email', $data['email'])->first();

        if (!$record || !$this->codeIsValid($record, $data['code'])) {
            throw ValidationException::withMessages([
                'code' => ['This password reset code is invalid or has expired.'],
            ]);
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user || $user->role !== 'driver') {
            throw ValidationException::withMessages([
                'email' => ['This email is not registered as a driver account.'],
            ]);
        }

        DB::transaction(function () use ($user, $data): void {
            $user->forceFill(['password' => $data['password']])->save();

            // Log every existing session out so a leaked token cannot be reused.
            $user->tokens()->delete();

            $this->tokenTable()->where('email', $user->email)->delete();
        });

        return response()->json([
            'message' => 'Your password has been reset. Please sign in with your new password.',
        ]);
    }

    /**
     * Check the submitted code against the stored hash and expiry window.
     */
    private function codeIsValid(object $record, string $code): bool
    {
        if (!$record->created_at) {
            return false;
        }

        if (Carbon::parse($record->created_at)->addMinutes($this->expiryMinutes())->isPast()) {
            return false;
        }

        return Hash::check($code, $record->token);
    }

    private function expiryMinutes(): int
    {
        return (int) config('auth.passwords.users.expire', 60);
    }

    private function tokenTable(): \Illuminate\Database\Query\Builder
    {
        return DB::table(config('auth.passwords.users.table', 'password_reset_tokens'));
    }
}

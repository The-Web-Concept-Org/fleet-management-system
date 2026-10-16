<?php

namespace App\Http\Controllers;

use App\Support\UserPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Lets an authenticated driver manage their own profile.
 *
 * Every driver detail can be edited except the login email, which stays under
 * dispatcher control. A password change requires the current password.
 */
class ProfileController extends Controller
{
    /**
     * Return the authenticated user's profile details.
     *
     * This is the payload the app renders on the profile screen. Send the
     * fields back to PUT/PATCH /api/profile to save changes.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('driver');

        return response()->json([
            'user' => UserPresenter::format($user, includeBus: false),
        ]);
    }

    /**
     * Update the authenticated driver's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'driver' || !$user->driver) {
            return response()->json([
                'message' => 'Only driver accounts have an editable driver profile.',
            ], 403);
        }

        $driver = $user->driver;

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'badge_id' => ['sometimes', 'required', 'string', 'max:50', 'unique:drivers,badge_id,' . $driver->id],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'license_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'license_type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'license_expiry' => ['sometimes', 'nullable', 'date'],
            'duty_status' => ['sometimes', 'required', 'string', 'in:available,on_route,off_duty,on_leave'],
            'experience_years' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:50'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['sometimes', 'nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (!empty($data['password']) && !Hash::check($data['current_password'] ?? '', $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        DB::transaction(function () use ($user, $driver, $data): void {
            $userUpdates = [];

            if (array_key_exists('name', $data)) {
                $userUpdates['name'] = $data['name'];
            }

            if (!empty($data['password'])) {
                $userUpdates['password'] = $data['password'];
            }

            if ($userUpdates) {
                $user->update($userUpdates);
            }

            $driverUpdates = Arr::only($data, [
                'badge_id',
                'phone',
                'license_number',
                'license_type',
                'license_expiry',
                'duty_status',
                'experience_years',
                'notes',
            ]);

            if (array_key_exists('badge_id', $driverUpdates) && $driverUpdates['badge_id'] !== null) {
                $driverUpdates['badge_id'] = strtoupper(trim($driverUpdates['badge_id']));
            }

            if ($driverUpdates) {
                $driver->update($driverUpdates);
            }
        });

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => UserPresenter::format($user->fresh(['driver']), includeBus: false),
        ]);
    }
}

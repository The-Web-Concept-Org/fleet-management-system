<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the application account & security settings view.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('settings', [
            'user' => $user,
        ]);
    }

    /**
     * Update user name and email.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update($validated);

        return back()->with('profile_status', 'Account profile updated successfully.');
    }

    /**
     * Change user password with current password verification.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'The provided current password does not match our records.',
            'password.confirmed' => 'New password confirmation does not match.',
            'password.min' => 'New password must be at least 6 characters.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('password_status', 'Security password changed successfully.');
    }
}

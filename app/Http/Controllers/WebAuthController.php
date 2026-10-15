<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    /**
     * Display the fleet login view.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle incoming web login authentication.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials do not match our fleet records.',
                ], 422);
            }

            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => 'The provided credentials do not match our fleet records.',
            ]);
        }

        if ($user->status === 'pending') {
            $msg = 'Your account is pending administrator approval before accessing Lancotri fleet operations.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return back()->withInput($request->only('email'))->withErrors(['email' => $msg]);
        }

        if ($user->status !== 'active') {
            $msg = 'This account has been deactivated. Please contact your dispatch coordinator.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return back()->withInput($request->only('email'))->withErrors(['email' => $msg]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken('fleet-web-' . $user->id)->plainTextToken;

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Authorization verified. Initializing Lancotri fleet console...',
                'redirect' => route('dashboard'),
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log out of the web session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out of Lancotri.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use App\Support\DriverMailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers with stats and filters.
     */
    public function index(Request $request): View
    {
        $query = Driver::with(['user', 'bus']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('badge_id', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%")
                    ->orWhereHas('bus', function ($bq) use ($search): void {
                        $bq->where('bus_number', 'like', "%{$search}%")
                            ->orWhere('plate', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search): void {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('duty_status') && $request->input('duty_status') !== 'all') {
            $query->where('duty_status', $request->input('duty_status'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            $query->whereHas('user', function ($uq) use ($status): void {
                $uq->where('status', $status);
            });
        }

        $drivers = $query->orderBy('created_at', 'desc')->get();

        $totalDrivers = Driver::count();
        $onRouteDrivers = Driver::where('duty_status', 'on_route')->count();
        $availableDrivers = Driver::where('duty_status', 'available')->count();
        $offDutyDrivers = Driver::whereIn('duty_status', ['off_duty', 'on_leave'])->count();
        $expiringLicensesCount = Driver::whereNotNull('license_expiry')
            ->where('license_expiry', '<=', now()->addDays(60))
            ->count();

        return view('drivers', compact(
            'drivers',
            'totalDrivers',
            'onRouteDrivers',
            'availableDrivers',
            'offDutyDrivers',
            'expiringLicensesCount'
        ));
    }

    /**
     * Store a newly created driver and their user account.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'badge_id' => ['required', 'string', 'max:50', 'unique:drivers,badge_id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'license_type' => ['nullable', 'string', 'max:50'],
            'license_expiry' => ['nullable', 'date'],
            'duty_status' => ['required', 'string', 'in:available,on_route,off_duty,on_leave'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $driver = DB::transaction(function () use ($validated): Driver {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'driver',
                'status' => $validated['status'],
            ]);

            return Driver::create([
                'user_id' => $user->id,
                'badge_id' => strtoupper(trim($validated['badge_id'])),
                'phone' => $validated['phone'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'license_type' => $validated['license_type'] ?? 'Class B CDL',
                'license_expiry' => $validated['license_expiry'] ?? null,
                'duty_status' => $validated['duty_status'],
                'experience_years' => $validated['experience_years'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        // Email the driver their login credentials (bus assignment comes later).
        DriverMailer::sendCredentials($driver->load('user'), $validated['password']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Driver {$driver->user->name} added successfully. Driver can log in with role 'driver'.",
                'driver' => $driver->load('user'),
            ]);
        }

        return redirect()->route('drivers')->with('success', "Driver {$driver->user->name} added successfully.");
    }

    /**
     * Update the specified driver and their login credentials.
     */
    public function update(Request $request, Driver $driver): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $driver->user_id],
            'password' => ['nullable', 'string', 'min:6'],
            'badge_id' => ['required', 'string', 'max:50', 'unique:drivers,badge_id,' . $driver->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'license_type' => ['nullable', 'string', 'max:50'],
            'license_expiry' => ['nullable', 'date'],
            'duty_status' => ['required', 'string', 'in:available,on_route,off_duty,on_leave'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $driver): void {
            $userPayload = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'driver',
                'status' => $validated['status'],
            ];

            if (!empty($validated['password'])) {
                $userPayload['password'] = Hash::make($validated['password']);
            }

            $driver->user->update($userPayload);

            $driver->update([
                'badge_id' => strtoupper(trim($validated['badge_id'])),
                'phone' => $validated['phone'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'license_type' => $validated['license_type'] ?? 'Class B CDL',
                'license_expiry' => $validated['license_expiry'] ?? null,
                'duty_status' => $validated['duty_status'],
                'experience_years' => $validated['experience_years'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Driver {$driver->user->name} updated successfully.",
                'driver' => $driver->fresh(['user']),
            ]);
        }

        return redirect()->route('drivers')->with('success', "Driver {$driver->user->name} updated successfully.");
    }

    /**
     * Remove the specified driver and their login user account.
     */
    public function destroy(Request $request, Driver $driver): JsonResponse|RedirectResponse
    {
        $driverName = $driver->user?->name ?? 'Driver';

        DB::transaction(function () use ($driver): void {
            $user = $driver->user;
            $driver->delete();
            if ($user) {
                $user->delete();
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Driver {$driverName} and login account deleted successfully.",
            ]);
        }

        return redirect()->route('drivers')->with('success', "Driver {$driverName} deleted successfully.");
    }
}

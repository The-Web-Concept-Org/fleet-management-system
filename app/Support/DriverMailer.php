<?php

namespace App\Support;

use App\Mail\BusAssignmentMail;
use App\Mail\DriverCredentialsMail;
use App\Models\Bus;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends driver-facing emails (credentials and bus assignments).
 *
 * Failures are logged and never break the request that triggered them, so a
 * misconfigured mailer can never block a bus/driver save.
 */
class DriverMailer
{
    /**
     * Email a newly created driver their login credentials.
     */
    public static function sendCredentials(Driver $driver, string $plainPassword): bool
    {
        return self::attempt('credentials', $driver, function () use ($driver, $plainPassword): void {
            Mail::to($driver->user->email)->send(new DriverCredentialsMail($driver, $plainPassword));
        });
    }

    /**
     * Email a driver that a bus has been assigned to them.
     */
    public static function sendBusAssignment(Bus $bus, Driver $driver): bool
    {
        return self::attempt('bus_assignment', $driver, function () use ($bus, $driver): void {
            Mail::to($driver->user->email)->send(new BusAssignmentMail($bus, $driver));
        });
    }

    private static function attempt(string $type, Driver $driver, callable $callback): bool
    {
        if (!$driver->user?->email) {
            return false;
        }

        try {
            $callback();

            return true;
        } catch (\Throwable $e) {
            Log::warning('Driver email failed: ' . $e->getMessage(), [
                'type' => $type,
                'driver_id' => $driver->id,
            ]);

            return false;
        }
    }
}

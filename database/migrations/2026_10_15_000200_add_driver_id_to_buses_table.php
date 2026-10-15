<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buses', function (Blueprint $table): void {
            // One bus may have at most one driver, and one driver at most one bus.
            $table->foreignId('driver_id')
                ->nullable()
                ->after('operator')
                ->constrained('drivers')
                ->nullOnDelete();

            $table->unique('driver_id');
        });

        // Backfill: the legacy free-text "operator" column held the driver's name.
        $taken = [];
        $buses = DB::table('buses')->whereNotNull('operator')->get(['id', 'operator']);

        foreach ($buses as $bus) {
            $driverId = DB::table('drivers')
                ->join('users', 'users.id', '=', 'drivers.user_id')
                ->where('users.name', $bus->operator)
                ->value('drivers.id');

            if ($driverId && !in_array($driverId, $taken, true)) {
                DB::table('buses')->where('id', $bus->id)->update(['driver_id' => $driverId]);
                $taken[] = $driverId;
            }
        }
    }

    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table): void {
            $table->dropUnique(['driver_id']);
            $table->dropConstrainedForeignId('driver_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links a fuel fill-up to the trip it was made during. The column is optional
 * and guarded so this is safe on the live database as well as a fresh install.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('fuel_logs', 'trip_id')) {
            return;
        }

        Schema::table('fuel_logs', function (Blueprint $table): void {
            $table->unsignedBigInteger('trip_id')->nullable()->after('shift_id');
        });

        // SQLite cannot add a foreign key to an existing table; other engines can.
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('fuel_logs', function (Blueprint $table): void {
                $table->foreign('trip_id')->references('id')->on('trips')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fuel_logs', 'trip_id')) {
            Schema::table('fuel_logs', function (Blueprint $table): void {
                $table->dropColumn('trip_id');
            });
        }
    }
};

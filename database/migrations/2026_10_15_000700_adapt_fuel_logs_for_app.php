<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adapts the pre-existing fuel_logs table so the app can store a receipt / pump
 * photo directly and log a fill-up without forcing a driver or odometer value.
 * Each step is guarded so this is safe to run on both the live database (where
 * the table was created by hand) and a freshly migrated database.
 */
return new class extends Migration
{
    public function up(): void
    {
        $columns = collect(Schema::getColumns('fuel_logs'))->keyBy('name');

        if (!isset($columns['image'])) {
            Schema::table('fuel_logs', function (Blueprint $table): void {
                $table->string('image')->nullable()->after('fuel_type');
            });
        }

        if (isset($columns['driver_id']) && !$columns['driver_id']['nullable']) {
            Schema::table('fuel_logs', function (Blueprint $table): void {
                $table->unsignedBigInteger('driver_id')->nullable()->change();
            });
        }

        if (isset($columns['odometer']) && !$columns['odometer']['nullable']) {
            Schema::table('fuel_logs', function (Blueprint $table): void {
                $table->integer('odometer')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fuel_logs', 'image')) {
            Schema::table('fuel_logs', function (Blueprint $table): void {
                $table->dropColumn('image');
            });
        }
    }
};

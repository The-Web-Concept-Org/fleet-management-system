<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bus assignment now lives exclusively on the buses table (buses.driver_id).
        Schema::table('drivers', function (Blueprint $table): void {
            $table->dropColumn('assigned_bus');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table): void {
            $table->string('assigned_bus')->nullable()->after('license_expiry');
        });
    }
};

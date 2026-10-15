<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('shift_number')->unique();

            // The vehicle the meter / fuel readings belong to. Kept alongside
            // the derived driver so the log survives if either is removed.
            $table->foreignId('bus_id')->nullable()->constrained('buses')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();

            // The scheduled trip (from the driver login response) this shift runs.
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();

            // Meter (odometer) readings captured from the dashboard photo.
            $table->integer('start_km');
            $table->integer('end_km')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();

            // Fuel detail recorded at shift start and end (level + optional liters).
            $table->string('start_fuel_level')->nullable();
            $table->decimal('start_fuel_liters', 8, 2)->nullable();
            $table->string('end_fuel_level')->nullable();
            $table->decimal('end_fuel_liters', 8, 2)->nullable();

            // Stored meter reading photos (public disk paths).
            $table->string('start_meter_image')->nullable();
            $table->string('end_meter_image')->nullable();

            $table->text('start_notes')->nullable();
            $table->text('end_notes')->nullable();

            $table->string('status')->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->timestamps();

            $table->index(['bus_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_logs');
    }
};

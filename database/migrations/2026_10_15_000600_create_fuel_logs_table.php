<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fuel / refuel log captured from the app: receipt photo, liters, fuel type,
 * price and the moment the fuel was added, alongside created_at / updated_at.
 *
 * The table already exists on the live database (created outside of migrations),
 * so creation is skipped there while a fresh install gets the same structure.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fuel_logs')) {
            return;
        }

        Schema::create('fuel_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->nullable()->unique();

            // Vehicle and driver the fill-up belongs to.
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('bus_id')->constrained('buses')->cascadeOnDelete();
            $table->unsignedBigInteger('shift_id')->nullable();

            // Core fill-up detail.
            $table->decimal('liters', 12, 2);
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->string('fuel_type')->nullable();

            // Where it was pumped.
            $table->string('station')->nullable();
            $table->string('pump_number', 32)->nullable();
            $table->string('nozzle', 32)->nullable();
            $table->string('fleet_card', 32)->nullable();
            $table->string('receipt_number', 64)->nullable();

            // Totals / derived consumption figures.
            $table->integer('odometer')->nullable();
            $table->integer('target_cap_liters')->nullable();
            $table->integer('distance_since_last_fill_km')->nullable();
            $table->decimal('efficiency_km_per_litre', 8, 2)->nullable();

            // Linked media (receipt image) and OCR metadata.
            $table->unsignedBigInteger('receipt_media_id')->nullable();
            $table->decimal('ocr_confidence', 5, 2)->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status', 24)->default('pending');
            $table->string('flagged_reason')->nullable();
            $table->timestamp('filled_at')->nullable();
            $table->text('note')->nullable();

            // Offline sync support.
            $table->uuid('client_uuid')->nullable()->unique();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->index(['driver_id', 'filled_at']);
            $table->index(['bus_id', 'filled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};

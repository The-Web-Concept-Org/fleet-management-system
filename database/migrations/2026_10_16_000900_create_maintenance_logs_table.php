<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vehicle maintenance log captured from the app: the bill / workshop invoice
 * photo, the amount spent, a free-text note and the vehicle, driver and trip it
 * belongs to, alongside created_at / updated_at.
 *
 * Creation is guarded so this is safe on a database where the table was made by
 * hand as well as on a fresh install.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('maintenance_logs')) {
            return;
        }

        Schema::create('maintenance_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->nullable()->unique();

            // Vehicle, driver and trip the maintenance belongs to.
            $table->foreignId('bus_id')->constrained('buses')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();

            // Core maintenance detail: amount spent and the note describing the work.
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('service_type')->nullable();
            $table->text('note')->nullable();

            $table->string('status', 24)->default('pending');
            $table->string('flagged_reason')->nullable();

            // Bill / workshop invoice image.
            $table->string('image')->nullable();

            $table->decimal('odometer', 12, 2)->nullable();

            // Offline sync support.
            $table->uuid('client_uuid')->nullable()->unique();
            $table->timestamp('performed_at')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->index(['bus_id', 'performed_at']);
            $table->index(['driver_id', 'performed_at']);
        });

        // SQLite cannot add a foreign key to an existing table; other engines can.
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('maintenance_logs', function (Blueprint $table): void {
                $table->foreign('trip_id')->references('id')->on('trips')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table): void {
            $table->id();
            $table->string('trip_number')->unique();
            $table->string('origin');
            $table->string('destination');
            $table->date('trip_date');
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();

            // A trip is tied to one vehicle; the driver is derived from that
            // bus's own assignment, so it is never duplicated on the trip.
            $table->foreignId('bus_id')->nullable()->constrained('buses')->nullOnDelete();

            $table->timestamps();

            $table->index(['trip_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};

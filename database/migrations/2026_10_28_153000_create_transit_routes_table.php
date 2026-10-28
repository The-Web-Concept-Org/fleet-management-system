<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transit_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_code', 30)->unique();
            $table->string('name', 150);
            $table->string('origin', 150);
            $table->string('destination', 150);
            $table->decimal('origin_lat', 10, 7)->nullable();
            $table->decimal('origin_lng', 10, 7)->nullable();
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();
            $table->decimal('distance_km', 8, 2)->default(0);
            $table->unsignedInteger('estimated_minutes')->default(45);
            $table->json('waypoints')->nullable();
            $table->json('path_coordinates')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transit_routes');
    }
};

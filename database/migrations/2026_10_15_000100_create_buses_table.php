<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table): void {
            $table->id();
            $table->string('bus_number')->unique();
            $table->string('plate')->unique();
            $table->string('model')->nullable();
            $table->string('operator')->nullable();
            $table->string('status')->default('standby');
            $table->integer('odometer')->default(0);
            $table->integer('fuel_month_liters')->default(0);
            $table->decimal('fuel_cost', 12, 2)->default(0);
            $table->decimal('km_per_liter', 5, 2)->nullable();
            $table->integer('next_service_km')->default(10000);
            $table->string('route')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};

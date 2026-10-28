<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_log_id')->constrained('shift_logs')->cascadeOnDelete();
            $table->foreignId('bus_id')->nullable()->constrained('buses')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('speed', 6, 2)->nullable();
            $table->decimal('heading', 5, 2)->nullable();
            $table->decimal('accuracy', 6, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->boolean('is_offline_sync')->default(false);
            $table->timestamps();

            $table->index(['shift_log_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_locations');
    }
};

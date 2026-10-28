<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            if (!Schema::hasColumn('buses', 'transit_route_id')) {
                $table->foreignId('transit_route_id')->nullable()->constrained('transit_routes')->nullOnDelete();
            }
            if (!Schema::hasColumn('buses', 'last_latitude')) {
                $table->decimal('last_latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('buses', 'last_longitude')) {
                $table->decimal('last_longitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('buses', 'last_location_at')) {
                $table->timestamp('last_location_at')->nullable();
            }
            if (!Schema::hasColumn('buses', 'is_online')) {
                $table->boolean('is_online')->default(false);
            }
            if (!Schema::hasColumn('buses', 'speed')) {
                $table->decimal('speed', 6, 2)->nullable();
            }
            if (!Schema::hasColumn('buses', 'heading')) {
                $table->decimal('heading', 5, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            if (Schema::hasColumn('buses', 'transit_route_id')) {
                $table->dropForeign(['transit_route_id']);
                $table->dropColumn('transit_route_id');
            }
            if (Schema::hasColumn('buses', 'is_online')) {
                $table->dropColumn('is_online');
            }
            if (Schema::hasColumn('buses', 'speed')) {
                $table->dropColumn('speed');
            }
            if (Schema::hasColumn('buses', 'heading')) {
                $table->dropColumn('heading');
            }
        });
    }
};

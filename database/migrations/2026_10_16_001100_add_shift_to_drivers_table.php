<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table): void {
            $table->string('shift')->nullable()->default('Morning (06:00 - 14:00)')->after('duty_status');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table): void {
            $table->dropColumn('shift');
        });
    }
};

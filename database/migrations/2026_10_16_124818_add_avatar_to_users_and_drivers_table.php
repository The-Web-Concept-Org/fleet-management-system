<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('duty_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};

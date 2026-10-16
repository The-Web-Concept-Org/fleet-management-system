<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('app_notifications')) {
            Schema::create('app_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('type', 60)->default('INFO');
                $table->string('title', 150);
                $table->text('body');
                $table->jsonb('data')->nullable();
                $table->string('link', 255)->nullable();
                $table->string('dedupe_key', 128)->nullable()->index();
                $table->timestamp('read_at')->nullable()->index();
                $table->timestamp('created_at')->useCurrent()->index();
            });
        }

        if (!Schema::hasTable('fcm_tokens')) {
            Schema::create('fcm_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('token', 512)->unique();
                $table->string('platform', 20)->default('WEB');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
        Schema::dropIfExists('app_notifications');
    }
};

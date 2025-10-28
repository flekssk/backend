<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('name');
            $t->timestamps();
        });

        Schema::create('role_user', function (Blueprint $t) {
            $t->uuid('user_id');
            $t->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $t->primary(['user_id', 'role_id']);
        });

        Schema::create('player_profiles', function (Blueprint $t) {
            $t->id();
            $t->uuid('user_id');
            $t->string('avatar', 300)->nullable();
            $t->string('current_currency', 10)->nullable();
            $t->unsignedBigInteger('vk_id')->nullable();
            $t->string('tg_id', 50)->nullable();
            $t->boolean('welcome_bonus_use')->default(false);
            $t->boolean('limit_payment')->default(false);
            $t->timestamps();
        });

        Schema::create('admin_profiles', function (Blueprint $t) {
            $t->id();
            $t->uuid('user_id');
            $t->string('title')->nullable();
            $t->json('permissions')->nullable();
            $t->timestamps();
        });

        Schema::create('wallets', function (Blueprint $t) {
            $t->id();
            $t->uuid('user_id');
            $t->string('currency_code', 10)->default('RUB');
            $t->decimal('balance', 20)->default(0);
            $t->unique(['user_id', 'currency_code']);
            $t->timestamps();
        });

        Schema::create('game_type', function (Blueprint $t) {
            $t->id();
            $t->string('slug', 8);
        });

        Schema::create('game_session', function (Blueprint $t) {
            $t->id();
            $t->uuid('user_id');
            $t->foreignId('game_type_id')->constrained('game_type')->cascadeOnDelete();
            $t->timestamps();
        });

        Schema::create('game_log', function (Blueprint $t) {
            $t->id();
            $t->uuid('user_id');
            $t->integer('game_type_id');
            $t->integer('game_id');
            $t->bigInteger('profit');
            $t->timestamps();
        });

        Schema::create('free_spins', function (Blueprint $t) {
            $t->id();
            $t->uuid('user_id');
            $t->unsignedBigInteger('slot_id')->nullable();
            $t->unsignedInteger('amount')->default(0);
            $t->timestamp('expires_at')->nullable();
            $t->string('source')->nullable();
            $t->timestamps();
        });

        Schema::create('user_bans', function (Blueprint $t) {
            $t->id();
            $t->uuid('user_id');
            $t->boolean('active')->default(true);
            $t->string('reason')->nullable();
            $t->timestamp('until_at')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bans');
        Schema::dropIfExists('free_spins');
        Schema::dropIfExists('game_stats');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('admin_profiles');
        Schema::dropIfExists('player_profiles');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};

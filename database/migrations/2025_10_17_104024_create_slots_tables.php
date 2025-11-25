<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alias');
            $table->string('image');
            $table->string('icon');
            $table->timestamps();
        });

        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('title', 128);
            $table->integer('slot_aggregator_id');
            $table->integer('slot_provider_id');
            $table->text('image');
            $table->string('alias', 64);
            $table->integer('priority')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->string('external_id', 16);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
        Schema::dropIfExists('slot_providers');
    }
};

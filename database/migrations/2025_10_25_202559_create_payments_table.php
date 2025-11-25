<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->integer('amount');
            $table->string('payment_provider');
            $table->string('payment_provider_method');
            $table->integer('status')->default(0);
            $table->enum('payment_source', ['socia', 'stimule'])->default('socia');
            $table->string('external_id', 64)->nullable();
            $table->timestamps();
        });
        Schema::create('withdraws', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->integer('amount');
            $table->string('wallet');
            $table->string('provider');
            $table->string('provider');
            $table->string('method');
            $table->string('variant')->nullable();
            $table->integer('status')->default(0);
            $table->string('external_id', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

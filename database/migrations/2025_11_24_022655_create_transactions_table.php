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
        Schema::create('transactions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();

        // Instead of ENUM
        $table->string('method')->default('cod'); // cod, card, paypal, paystack
        $table->string('gateway')->nullable();     // paystack, paypal, stripe, etc

        $table->string('status')->default('pending'); // pending, approved, declined, refunded

        $table->string('reference')->nullable();
        $table->json('gateway_response')->nullable();

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

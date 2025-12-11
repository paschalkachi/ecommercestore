<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'paystack' to the transactions.mode enum (MySQL)
+        DB::statement("ALTER TABLE `transactions` MODIFY `mode` ENUM('cod','card','paypal','paystack') NOT NULL");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum to previous values
+        DB::statement("ALTER TABLE `transactions` MODIFY `mode` ENUM('cod','card','paypal') NOT NULL");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add new columns
            $table->string('gateway')->nullable()->after('order_id'); // online processor
            $table->string('method')->default('cod')->after('gateway'); // cod, card, bank transfer, etc.

            // Change status from enum to string
            $table->string('status')->default('pending')->change();

            // Change gateway_response to JSON
            $table->json('gateway_response')->nullable()->change();
        });

        // Migrate old 'mode' data
        DB::table('transactions')->get()->each(function ($txn) {
            $gateway = in_array($txn->mode, ['cod']) ? null : $txn->mode;
            $method = $txn->mode; // COD, card, paypal, paystack

            DB::table('transactions')
                ->where('id', $txn->id)
                ->update([
                    'gateway' => $gateway,
                    'method' => $method,
                ]);
        });

        // Drop old 'mode' column
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('mode');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Recreate old enum column
            DB::statement("ALTER TABLE `transactions` ADD `mode` ENUM('cod','card','paypal','paystack') AFTER `order_id` NOT NULL DEFAULT 'cod'");

            // Restore old 'mode' from method
            DB::table('transactions')->get()->each(function ($txn) {
                $mode = $txn->method; // revert method to mode
                DB::table('transactions')
                    ->where('id', $txn->id)
                    ->update([
                        'mode' => $mode,
                    ]);
            });

            $table->dropColumn(['gateway', 'method']);
            $table->string('status')->default('pending')->change();
        });
    }
};

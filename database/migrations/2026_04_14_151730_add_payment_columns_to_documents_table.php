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
        $tables = ['petty_cash', 'cash_advance_draw', 'international_trip', 'supplier_payment'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->boolean('is_paid')->default(false)->after('hardfile_received_by');
                $table->timestamp('paid_at')->nullable()->after('is_paid');
                $table->foreignId('paid_by')->nullable()->constrained('users')->after('paid_at');
                $table->string('payment_receipt_path')->nullable()->after('paid_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['petty_cash', 'cash_advance_draw', 'international_trip', 'supplier_payment'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['paid_by']);
                $table->dropColumn(['is_paid', 'paid_at', 'paid_by', 'payment_receipt_path']);
            });
        }
    }
};

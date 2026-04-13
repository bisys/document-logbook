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
        $tables = [
            'supplier_payment',
            'petty_cash',
            'cash_advance_draw',
            'cash_advance_realization',
            'international_trip',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->timestamp('hardfile_received_at')->nullable()->after('document_status_id');
                $blueprint->unsignedBigInteger('hardfile_received_by')->nullable()->after('hardfile_received_at');
                $blueprint->foreign('hardfile_received_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'supplier_payment',
            'petty_cash',
            'cash_advance_draw',
            'cash_advance_realization',
            'international_trip',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropForeign([$table . '_hardfile_received_by_foreign']);
                $blueprint->dropColumn(['hardfile_received_at', 'hardfile_received_by']);
            });
        }
    }
};

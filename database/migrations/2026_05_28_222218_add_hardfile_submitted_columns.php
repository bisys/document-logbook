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
            'international_trip_realization',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->boolean('is_hardfile_submitted')->default(false)->after('hardfile_received_by');
                $blueprint->timestamp('hardfile_submitted_at')->nullable()->after('is_hardfile_submitted');
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
            'international_trip_realization',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn(['is_hardfile_submitted', 'hardfile_submitted_at']);
            });
        }
    }
};

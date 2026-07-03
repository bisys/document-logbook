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
        $tables = [
            'supplier_payment',
            'petty_cash',
            'cash_advance_draw',
            'international_trip',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasColumn($table, 'purpose')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('purpose')->default('')->nullable(false);
                });
            }
        }

        foreach ($tables as $table) {
            DB::table($table)
                ->whereNull('purpose')
                ->update(['purpose' => '']);
        }

        if (Schema::getConnection()->getDriverName() === 'sqlsrv') {
            foreach ($tables as $table) {
                DB::statement("ALTER TABLE [{$table}] ALTER COLUMN [purpose] NVARCHAR(255) NOT NULL");
            }
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
            'international_trip',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'purpose')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('purpose');
                });
            }
        }
    }
};

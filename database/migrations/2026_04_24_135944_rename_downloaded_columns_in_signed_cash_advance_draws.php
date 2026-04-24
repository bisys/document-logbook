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
        Schema::table('signed_cash_advance_draws', function (Blueprint $table) {
            $table->renameColumn('is_downloaded', 'is_opened');
            $table->renameColumn('downloaded_at', 'opened_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signed_cash_advance_draws', function (Blueprint $table) {
            $table->renameColumn('is_opened', 'is_downloaded');
            $table->renameColumn('opened_at', 'downloaded_at');
        });
    }
};

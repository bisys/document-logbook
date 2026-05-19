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
        Schema::rename('signed_cash_advance_draws', 'signed_documents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('signed_documents', 'signed_cash_advance_draws');
    }
};

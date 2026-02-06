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
        Schema::create('cash_advance_realization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_advance_draw_id')->constrained('cash_advance_draw')->onDelete('restrict');
            $table->string('number')->unique();
            $table->string('car_form');
            $table->string('original_invoice');
            $table->string('copy_invoice');
            $table->string('internal_memo_entertain')->nullable();
            $table->string('entertain_realization_form')->nullable();
            $table->string('minutes_of_meeting')->nullable();
            $table->string('nominative_summary')->nullable();
            $table->string('cic_form')->nullable();
            $table->string('budget_plan');
            $table->foreignId('document_status_id')->constrained('document_statuses')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_advance_realization');
    }
};

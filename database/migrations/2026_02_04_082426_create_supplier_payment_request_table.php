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
        Schema::create('supplier_payment', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('cost_center_id')->constrained('cost_centers')->onDelete('restrict');
            $table->string('spr_form');
            $table->string('document_number')->unique();
            $table->string('original_invoice');
            $table->string('copy_invoice');
            $table->string('tax_invoice');
            $table->string('agreement');
            $table->string('internal_memo_entertain')->nullable();
            $table->string('entertain_realization_form')->nullable();
            $table->string('minutes_of_meeting')->nullable();
            $table->string('nominative_summary')->nullable();
            $table->string('calculation_summary')->nullable();
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
        Schema::dropIfExists('supplier_payment');
    }
};

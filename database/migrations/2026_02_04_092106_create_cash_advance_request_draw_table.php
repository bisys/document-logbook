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
        Schema::create('cash_advance_draw', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('cost_center_id')->constrained('cost_centers')->onDelete('restrict');
            $table->string('car_form');
            $table->string('document_number')->unique();
            $table->string('proposal_or_monitor_budget');
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
        Schema::dropIfExists('cash_advance_draw');
    }
};

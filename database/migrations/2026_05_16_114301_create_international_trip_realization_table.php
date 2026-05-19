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
        Schema::create('international_trip_realization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('international_trip_id')->constrained('international_trip')->onDelete('no action');
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('no action');
            $table->foreignId('cost_center_id')->constrained('cost_centers')->onDelete('no action');
            $table->string('transfer_evidence');
            $table->string('other_document')->nullable();
            $table->unsignedInteger('edit_count')->default(0);
            $table->timestamp('hardfile_received_at')->nullable();
            $table->unsignedBigInteger('hardfile_received_by')->nullable();
            $table->foreign('hardfile_received_by')->references('id')->on('users')->onDelete('set null');
            $table->foreignId('document_status_id')->constrained('document_statuses')->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('international_trip_realization');
    }
};


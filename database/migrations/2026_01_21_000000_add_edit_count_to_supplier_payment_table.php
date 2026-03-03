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
        Schema::table('supplier_payment', function (Blueprint $table) {
            $table->unsignedInteger('edit_count')->default(0)->after('document_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payment', function (Blueprint $table) {
            $table->dropColumn('edit_count');
        });
    }
};

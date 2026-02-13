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
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('approval');
            $table->dropColumn('slug');
            $table->after('id', function (Blueprint $table) {
                $table->morphs('approvable');
                $table->foreignId('approval_role_id')->constrained('approval_roles');
                $table->foreignId('user_id')->nullable()->constrained('users');
                $table->foreignId('approval_status_id')->constrained('approval_statuses');
                $table->timestamp('approval_at')->nullable();
                $table->text('remark')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

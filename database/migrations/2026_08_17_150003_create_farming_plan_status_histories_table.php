<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farming_plan_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farming_plan_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('farming_plan_id', 'fk_fpl_hist_fpl')
                ->references('id')->on('farming_plans')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_fpl_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['farming_plan_id', 'created_at'], 'idx_fpl_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farming_plan_status_histories');
    }
};

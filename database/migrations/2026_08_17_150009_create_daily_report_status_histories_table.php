<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_report_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('daily_report_id', 'fk_drp_hist_drp')
                ->references('id')->on('daily_reports')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_drp_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['daily_report_id', 'created_at'], 'idx_drp_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_status_histories');
    }
};

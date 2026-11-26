<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_plot_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('land_plot_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('land_plot_id', 'fk_lpl_hist_lpl')
                ->references('id')->on('land_plots')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_lpl_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['land_plot_id', 'created_at'], 'idx_lpl_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_plot_status_histories');
    }
};

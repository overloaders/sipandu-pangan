<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wbp_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wbp_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('wbp_id', 'fk_wbp_hist_wbp')
                ->references('id')->on('wbp')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_wbp_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['wbp_id', 'created_at'], 'idx_wbp_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wbp_status_histories');
    }
};

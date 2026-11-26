<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distribution_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('distribution_id', 'fk_dst_hist_dst')
                ->references('id')->on('distributions')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_dst_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['distribution_id', 'created_at'], 'idx_dst_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_status_histories');
    }
};

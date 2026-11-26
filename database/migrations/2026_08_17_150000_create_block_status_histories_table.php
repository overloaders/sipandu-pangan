<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('block_id', 'fk_blk_hist_blk')
                ->references('id')->on('blocks')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_blk_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['block_id', 'created_at'], 'idx_blk_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_status_histories');
    }
};

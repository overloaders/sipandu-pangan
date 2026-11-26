<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_program_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_program_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('food_program_id', 'fk_fpg_hist_fpg')
                ->references('id')->on('food_programs')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_fpg_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['food_program_id', 'created_at'], 'idx_fpg_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_program_status_histories');
    }
};

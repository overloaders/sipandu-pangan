<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_program_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_program_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('training_program_id', 'fk_trp_hist_trp')
                ->references('id')->on('training_programs')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_trp_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['training_program_id', 'created_at'], 'idx_trp_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_program_status_histories');
    }
};

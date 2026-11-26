<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_barn_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('livestock_barn_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('livestock_barn_id', 'fk_lbh_hist_lbh')
                ->references('id')->on('livestock_barns')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_lbh_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['livestock_barn_id', 'created_at'], 'idx_lbh_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_barn_status_histories');
    }
};

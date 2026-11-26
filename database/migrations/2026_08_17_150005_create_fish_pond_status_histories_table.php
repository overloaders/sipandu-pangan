<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fish_pond_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fish_pond_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('fish_pond_id', 'fk_fph_hist_fph')
                ->references('id')->on('fish_ponds')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_fph_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['fish_pond_id', 'created_at'], 'idx_fph_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fish_pond_status_histories');
    }
};

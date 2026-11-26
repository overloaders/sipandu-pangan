<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('room_id', 'fk_rom_hist_rom')
                ->references('id')->on('rooms')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_rom_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['room_id', 'created_at'], 'idx_rom_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_status_histories');
    }
};

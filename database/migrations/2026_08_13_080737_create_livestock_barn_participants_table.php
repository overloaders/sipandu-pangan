<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('livestock_barn_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_barn_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wbp_id')->constrained('wbp')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dikeluarkan'])->default('aktif');
            $table->string('peran', 100)->nullable();
            $table->string('keterangan', 500)->nullable();
            $table->timestamps();
            $table->unique(['livestock_barn_id', 'wbp_id']);
            $table->index(['wbp_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestock_barn_participants');
    }
};

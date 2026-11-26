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
        Schema::create('fish_pond_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fish_pond_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wbp_id')->constrained('wbp')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dikeluarkan'])->default('aktif');
            $table->string('peran', 100)->nullable();
            $table->string('keterangan', 500)->nullable();
            $table->timestamps();
            $table->unique(['fish_pond_id', 'wbp_id']);
            $table->index(['wbp_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish_pond_participants');
    }
};

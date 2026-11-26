<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_program_id')->constrained()->onDelete('cascade');
            $table->foreignId('wbp_id')->constrained('wbp')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'keluar', 'dikeluarkan'])->default('aktif');
            $table->string('peran', 50)->nullable()->comment('Koordinator, anggota, tamping');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['food_program_id', 'wbp_id']);
            $table->index(['wbp_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_participants');
    }
};

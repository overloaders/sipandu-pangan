<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_certifications', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sertifikat', 30)->unique();
            $table->foreignId('wbp_id')->constrained('wbp')->onDelete('cascade');
            $table->foreignId('training_program_id')->constrained()->onDelete('cascade');
            $table->string('nama_sertifikat', 200);
            $table->string('nomor_sertifikat_resmi', 50);
            $table->date('tanggal_terbit');
            $table->date('masa_berlaku')->nullable();
            $table->string('lembaga_sertifikasi', 200)->nullable();
            $table->enum('tingkat', ['dasar', 'menengah', 'lanjutan', 'profesional']);
            $table->string('file_sertifikat', 255)->nullable();
            $table->enum('status', ['aktif', 'kedaluwarsa', 'dibatalkan']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['wbp_id', 'status']);
            $table->index(['training_program_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_certifications');
    }
};

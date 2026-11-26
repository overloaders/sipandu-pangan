<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_records', function (Blueprint $table) {
            $table->id();
            $table->string('kode_komplians', 30)->unique();
            $table->string('judul', 255);
            $table->enum('tipe', ['food_safety', 'sertifikasi_produk', 'standar_operasional', 'lingkungan', 'keamanan_kerja']);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_pemeriksaan');
            $table->date('tanggal_berlaku_sampai')->nullable();
            $table->enum('hasil', ['lulus', 'bersyarat', 'tidak_lulus', 'belum_dinilai']);
            $table->string('nilai_skor', 20)->nullable()->comment('Skor penilaian');
            $table->enum('tingkat_risiko', ['rendah', 'sedang', 'tinggi', 'kritis']);
            $table->foreignId('inspektur_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'aktif', 'perlu_perbaikan', 'kedaluwarsa', 'dibatalkan']);
            $table->text('temuan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->string('dokumen_pendukung', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'tipe']);
            $table->index(['status', 'tanggal_berlaku_sampai']);
            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_records');
    }
};

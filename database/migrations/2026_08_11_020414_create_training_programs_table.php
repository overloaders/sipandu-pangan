<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelatihan', 30)->unique();
            $table->string('nama_pelatihan', 200);
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['pertanian', 'peternakan', 'perikanan', 'pengolahan_pangan', 'keterampilan_umum', 'literasi', 'keagamaan']);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('jam_pelatihan')->default(0);
            $table->integer('kapasitas_peserta')->default(0);
            $table->decimal('biaya', 12, 2)->default(0);
            $table->string('lokasi', 200)->nullable();
            $table->enum('tipe', ['offline', 'online', 'hybrid']);
            $table->enum('status', ['perencanaan', 'terbuka', 'berlangsung', 'selesai', 'dibatalkan'])->default('perencanaan');
            $table->boolean('sertifikat_diberikan')->default(false);
            $table->text('materi_pokok')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index(['kategori', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 30)->unique();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_laporan');
            $table->foreignId('dilaporkan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'dikirim', 'dikonfirmasi', 'dikembalikan']);
            $table->integer('total_wbp_aktif')->default(0);
            $table->integer('total_wbp_baru_masuk')->default(0);
            $table->integer('total_wbp_keluar')->default(0);
            $table->decimal('total_panen_hari_ini_kg', 10, 2)->default(0);
            $table->decimal('total_pakan_hari_ini_kg', 10, 2)->default(0);
            $table->decimal('total_pendapatan_hari_ini', 15, 2)->default(0);
            $table->decimal('total_pengeluaran_hari_ini', 15, 2)->default(0);
            $table->text('ringkasan_kegiatan')->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->text('masalah_ditemukan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'tanggal_laporan']);
            $table->index(['status', 'tanggal_laporan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};

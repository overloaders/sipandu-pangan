<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained()->onDelete('cascade');
            $table->enum('kategori_laporan', ['pertanian', 'peternakan', 'perikanan', 'inventaris', 'keuangan', 'keamanan', 'kesehatan', 'pelatihan', 'lainnya']);
            $table->string('judul_detail', 200);
            $table->text('deskripsi');
            $table->decimal('nilai_angka', 12, 2)->nullable();
            $table->string('satuan', 30)->nullable();
            $table->string('foto_pendukung', 255)->nullable();
            $table->timestamps();

            $table->index(['daily_report_id', 'kategori_laporan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_details');
    }
};

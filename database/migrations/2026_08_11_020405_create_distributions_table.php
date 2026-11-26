<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_distribusi', 30)->unique();
            $table->enum('tipe_distribusi', ['konsumsi_wbp', 'penjualan_lokal', 'penjualan_eksternal', 'donasi', 'konversi_produksi']);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_distribusi');
            $table->decimal('total_nilai', 15, 2)->default(0);
            $table->integer('jumlah_penerima')->default(0)->comment('Jumlah WBP penerima/konsumen');
            $table->string('penerima_nama', 150)->nullable();
            $table->string('penerima_kontak', 50)->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->enum('status', ['draft', 'dikirim', 'diterima', 'dibatalkan'])->default('draft');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'tipe_distribusi']);
            $table->index(['tanggal_distribusi', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};

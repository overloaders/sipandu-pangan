<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processing_batches', function (Blueprint $table) {
            $table->id();
            $table->string('kode_batch', 30)->unique();
            $table->string('nama_produk', 200);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_pengolahan', ['pengeringan', 'pengalengan', 'pembekuan', 'fermentasi', 'penggilingan', 'pengepakan', 'pengolahan_umum']);
            $table->date('tanggal_produksi');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->decimal('jumlah_produksi', 10, 2);
            $table->string('satuan_output', 30)->default('kg');
            $table->decimal('biaya_produksi', 12, 2)->default(0);
            $table->decimal('harga_jual_satuan', 12, 2)->default(0);
            $table->enum('status', ['draft', 'proses', 'selesai', 'gagal']);
            $table->foreignId('pic_wbp_id')->nullable()->constrained('wbp')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'jenis_pengolahan']);
            $table->index(['tanggal_produksi', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processing_batches');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi_keu', 30)->unique();
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->foreignId('category_id')->nullable()->constrained('financial_categories')->onDelete('set null');
            $table->foreignId('budget_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('jumlah', 15, 2);
            $table->string('deskripsi', 255);
            $table->enum('sumber_dana', ['anggaran', 'penjualan_pangan', 'pnbp', 'hibah', 'sumbangan', 'lainnya']);
            $table->string('bukti_transaksi', 255)->nullable()->comment('Path file bukti');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris', 'lainnya']);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'pending_approval', 'disetujui', 'ditolak', 'selesai']);
            $table->date('tanggal_transaksi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'tipe']);
            $table->index(['tanggal_transaksi', 'tipe']);
            $table->index(['sumber_dana', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};

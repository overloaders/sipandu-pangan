<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 30)->unique();
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('tipe_transaksi', ['masuk', 'keluar', 'transfer', 'opname', 'rusak', 'kadaluarsa']);
            $table->decimal('jumlah', 12, 2);
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('total_nilai', 15, 2)->default(0);
            $table->enum('sumber', ['produksi', 'pembelian', 'konsumsi_wbp', 'penjualan', 'donasi', 'konversi', 'opname', 'lainnya']);
            $table->foreignId('ref_unit_id')->nullable()->constrained('units')->comment('Unit tujuan/sumber transfer');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_transaksi');
            $table->string('bukti_transaksi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'tipe_transaksi']);
            $table->index(['tanggal_transaksi', 'tipe_transaksi']);
            $table->index('sumber');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};

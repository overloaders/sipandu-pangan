<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_item', 30)->unique();
            $table->string('nama_item', 200);
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->onDelete('set null');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('satuan', 30)->default('kg');
            $table->decimal('stok_saat_ini', 12, 2)->default(0);
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->decimal('stok_maksimum', 12, 2)->default(0);
            $table->decimal('harga_beli_rata', 12, 2)->default(0);
            $table->decimal('harga_jual', 12, 2)->default(0);
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->enum('tipe_pangan', ['basah', 'kering', 'olahan', 'bumbu', 'bahan_baku']);
            $table->enum('asal', ['produksi_sendiri', 'pembelian', 'hibah', 'konversi']);
            $table->enum('status', ['tersedia', 'stok_habis', 'kadaluarsa', 'rusak', 'nonaktif']);
            $table->string('lokasi_simpan', 100)->nullable();
            $table->string('foto', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};

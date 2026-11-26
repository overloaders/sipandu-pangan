<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori', 20)->unique();
            $table->string('nama_kategori', 100);
            $table->enum('tipe', ['pangan', 'non_pangan', 'bahan_baku', 'produk_olahan', 'obat']);
            $table->string('satuan_standar', 30)->default('kg');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_categories');
    }
};

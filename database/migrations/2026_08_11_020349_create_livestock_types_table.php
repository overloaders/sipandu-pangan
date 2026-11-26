<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_types', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jenis', 20)->unique();
            $table->string('nama_jenis', 100);
            $table->enum('kategori', ['unggas', 'ruminansia', 'ungulates', 'fish', 'lainnya']);
            $table->decimal('berat_rata_kg', 8, 2)->nullable();
            $table->decimal('harga_beli_per_ekor', 12, 2)->nullable();
            $table->decimal('harga_jual_per_kg', 12, 2)->nullable();
            $table->integer('masa_pemeliharaan_hari')->nullable()->comment('Hari sampai siap panen');
            $table->string('pakan_utama', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_types');
    }
};

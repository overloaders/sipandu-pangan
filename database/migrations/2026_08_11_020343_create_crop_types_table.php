<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_types', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tanaman', 20)->unique();
            $table->string('nama_tanaman', 100);
            $table->string('nama_latin', 150)->nullable();
            $table->enum('kategori', ['sayuran', 'buah', 'padi', 'umbi', 'rempah', 'tanaman_perindustrian', 'tanaman_obat']);
            $table->integer('masa_tanam_hari')->comment('Masa tanam dalam hari');
            $table->decimal('hasil_per_m2_kg', 8, 2)->nullable()->comment('Estimasi hasil per m2');
            $table->decimal('harga_jual_per_kg', 12, 2)->nullable();
            $table->string('musim_tanam', 50)->nullable()->comment('Jan-Des atau sepanjang tahun');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_types');
    }
};

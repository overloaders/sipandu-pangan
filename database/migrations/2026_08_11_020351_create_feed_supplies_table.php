<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_supplies', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pakan', 30)->unique();
            $table->string('nama_pakan', 150);
            $table->enum('jenis_pakan', ['konsentrat', 'hijauan', 'kasar', 'premix', 'vitamin', 'obat']);
            $table->string('satuan', 20)->default('kg');
            $table->decimal('stok', 10, 2)->default(0);
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->decimal('harga_per_satuan', 12, 2);
            $table->string('supplier', 150)->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->enum('status', ['tersedia', 'habis', 'kadaluarsa', 'rusak']);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jenis_pakan', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_supplies');
    }
};

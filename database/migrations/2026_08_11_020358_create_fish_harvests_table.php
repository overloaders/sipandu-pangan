<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fish_harvests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_panen_ikan', 30)->unique();
            $table->foreignId('fish_stock_id')->constrained('fish_stock')->onDelete('cascade');
            $table->foreignId('pond_id')->constrained('fish_ponds')->onDelete('cascade');
            $table->date('tanggal_panen');
            $table->integer('jumlah_dipanen');
            $table->decimal('total_berat_kg', 10, 2);
            $table->decimal('ukuran_rata_cm', 6, 2)->nullable();
            $table->decimal('harga_jual_per_kg', 12, 2);
            $table->decimal('total_pendapatan', 15, 2);
            $table->decimal('total_biaya', 15, 2)->default(0)->comment('Biaya pakan + operasional');
            $table->enum('kualitas', ['A', 'B', 'C', 'D']);
            $table->string('tujuan', 50)->comment('konsumsi_wbp, dijual, pengolahan');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['pond_id', 'tanggal_panen']);
            $table->index(['fish_stock_id', 'tanggal_panen']);
            $table->index('tanggal_panen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fish_harvests');
    }
};

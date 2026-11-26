<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fish_stock', function (Blueprint $table) {
            $table->id();
            $table->string('kode_stok_ikan', 30)->unique();
            $table->foreignId('pond_id')->constrained('fish_ponds')->onDelete('cascade');
            $table->string('jenis_ikan', 100)->comment('Lele, Nila, Gurame, Patin, dll');
            $table->enum('kategori', ['benih', 'pembesaran', 'siap_panen']);
            $table->integer('jumlah');
            $table->decimal('ukuran_rata_cm', 6, 2)->nullable();
            $table->decimal('berat_rata_gram', 8, 2)->nullable();
            $table->date('tanggal_tebur');
            $table->decimal('harga_benih', 12, 2)->default(0);
            $table->decimal('total_biaya_pakan', 15, 2)->default(0);
            $table->enum('status', ['hidup', 'mati', 'dijual', 'dipanen']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pond_id', 'status']);
            $table->index(['jenis_ikan', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fish_stock');
    }
};

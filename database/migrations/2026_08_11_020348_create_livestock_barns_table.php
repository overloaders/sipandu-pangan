<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_barns', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kandang', 30)->unique();
            $table->string('nama_kandang', 150);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_ternak', ['ayam', 'bebek', 'kambing', 'sapi', 'babi', 'kelinci', 'kambing_perah', 'sapi_perah', 'lainnya']);
            $table->integer('kapasitas');
            $table->integer('terisi')->default(0);
            $table->decimal('luas_m2', 10, 2);
            $table->enum('tipe_kandang', ['kandang_baterai', 'kandang_umbaran', 'kandang_koloni', 'semi_ekstensif']);
            $table->enum('status', ['aktif', 'renovasi', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_barns');
    }
};

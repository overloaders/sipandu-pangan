<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_plots', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lahan', 30)->unique();
            $table->string('nama_lahan', 150);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->decimal('luas_m2', 12, 2);
            $table->enum('jenis_tanah', ['tanah_liat', 'tanah_berpasir', 'tanah_laterit', 'tanah_gambut', 'tanah_volkanik', 'campuran'])->nullable();
            $table->enum('sistem_irigasi', ['tadah_hujan', 'irigasi_tetes', 'parit', 'pompa_air', 'sungai', 'sumur']);
            $table->enum('status', ['tersedia', 'ditanami', 'istirahat', 'renovasi', 'tidak_digunakan'])->default('tersedia');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_plots');
    }
};

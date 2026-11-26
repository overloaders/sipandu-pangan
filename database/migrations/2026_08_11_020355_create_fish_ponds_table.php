<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fish_ponds', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kolam', 30)->unique();
            $table->string('nama_kolam', 150);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->decimal('luas_m2', 10, 2);
            $table->decimal('kedalaman_m', 5, 2)->nullable();
            $table->decimal('volume_m3', 10, 2)->nullable();
            $table->enum('tipe_kolam', ['tanah', 'semen', 'terpal', 'beton', 'floating_cage']);
            $table->enum('sumber_air', ['sungai', 'sumur', 'hujan', 'pompa', 'PDAM']);
            $table->enum('status', ['aktif', 'kering', 'renovasi', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fish_ponds');
    }
};

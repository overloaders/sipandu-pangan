<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('kode_indikator', 30)->unique();
            $table->string('nama_indikator', 200);
            $table->enum('kategori', ['produktivitas', 'efisiensi', 'kualitas_pangan', 'keuangan', 'sdm', 'lingkungan', 'sosial']);
            $table->text('deskripsi')->nullable();
            $table->string('rumus_perhitungan', 500)->nullable();
            $table->string('satuan', 50);
            $table->decimal('target_nilai', 12, 2)->nullable();
            $table->decimal('batas_minimum', 12, 2)->nullable();
            $table->decimal('batas_maksimum', 12, 2)->nullable();
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kategori', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_indicators');
    }
};

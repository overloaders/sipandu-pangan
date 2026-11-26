<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained('analytics_indicators')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->integer('periode_tahun');
            $table->integer('periode_bulan')->nullable();
            $table->integer('periode_triwulan')->nullable();
            $table->decimal('target_nilai', 12, 2);
            $table->decimal('realisasi_nilai', 12, 2)->default(0);
            $table->decimal('persentase_capaian', 5, 2)->default(0);
            $table->enum('status_capaian', ['di_bawah_target', 'sesuai_target', 'melebihi_target']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['indicator_id', 'unit_id', 'periode_tahun']);
            $table->index(['unit_id', 'periode_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_targets');
    }
};

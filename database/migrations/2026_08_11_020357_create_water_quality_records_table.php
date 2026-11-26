<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_quality_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pond_id')->constrained('fish_ponds')->onDelete('cascade');
            $table->date('tanggal_pemeriksaan');
            $table->time('jam_pemeriksaan');
            $table->decimal('suhu_celsius', 5, 2)->nullable();
            $table->decimal('ph', 5, 2)->nullable();
            $table->decimal('dissolved_oxygen_mgl', 6, 2)->nullable()->comment('DO');
            $table->decimal('amonia_mgl', 6, 3)->nullable();
            $table->decimal('nitrit_mgl', 6, 3)->nullable();
            $table->decimal('nitrat_mgl', 6, 3)->nullable();
            $table->decimal('kekeruhan_ntu', 6, 2)->nullable();
            $table->decimal('salinitas_ppt', 6, 2)->nullable();
            $table->enum('kondisi_air', ['baik', 'kurang_baik', 'buruk', 'sangat_buruk']);
            $table->text('tindakan_perlukan')->nullable();
            $table->string('pemeriksa_id', 50)->nullable();
            $table->timestamps();

            $table->index(['pond_id', 'tanggal_pemeriksaan']);
            $table->index(['tanggal_pemeriksaan', 'kondisi_air']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_quality_records');
    }
};

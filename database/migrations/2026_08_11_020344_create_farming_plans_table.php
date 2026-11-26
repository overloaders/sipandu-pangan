<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farming_plans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rencana', 30)->unique();
            $table->foreignId('food_program_id')->constrained()->onDelete('cascade');
            $table->foreignId('land_plot_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_type_id')->constrained()->onDelete('cascade');
            $table->integer('jumlah_tanaman');
            $table->date('tanggal_tanam_rencana');
            $table->date('tanggal_tanam_aktual')->nullable();
            $table->date('tanggal_panen_rencana')->nullable();
            $table->date('tanggal_panen_aktual')->nullable();
            $table->decimal('luas_tanam_m2', 10, 2);
            $table->enum('metode_budi_daya', ['konvensional', 'organik', 'hidroponik', 'aeroponik', 'mixed']);
            $table->enum('status', ['direncanakan', 'proses_tanam', 'tumbuh', 'siap_panen', 'panen', 'selesai'])->default('direncanakan');
            $table->decimal('estimasi_hasil_kg', 10, 2)->nullable();
            $table->decimal('estimasi_pendapatan', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['land_plot_id', 'status']);
            $table->index(['food_program_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farming_plans');
    }
};

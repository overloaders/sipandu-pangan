<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rencana_gizi', 30)->unique();
            $table->string('nama_menu', 200);
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->enum('waktu_makan', ['sarapan', 'makan_siang', 'makan_sore', 'snack_pagi', 'snack_sore']);
            $table->date('berlaku_mulai');
            $table->date('berlaku_selesai')->nullable();
            $table->decimal('kalori_target_kcal', 8, 2)->nullable();
            $table->decimal('protein_target_gr', 8, 2)->nullable();
            $table->decimal('karbohidrat_target_gr', 8, 2)->nullable();
            $table->decimal('lemak_target_gr', 8, 2)->nullable();
            $table->decimal('serat_target_gr', 8, 2)->nullable();
            $table->enum('tipe_porsi', ['standar', 'diet_khusus', 'vegan', 'bayi', 'lansia']);
            $table->decimal('estimasi_biaya_per_porsi', 10, 2)->default(0);
            $table->enum('sumber_bahan', ['produksi_sendiri', 'campuran', 'pembelian']);
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['draft', 'aktif', 'nonaktif'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'waktu_makan', 'status']);
            $table->index(['berlaku_mulai', 'berlaku_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_plans');
    }
};

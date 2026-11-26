<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_anggaran', 30)->unique();
            $table->foreignId('food_program_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('financial_categories')->onDelete('set null');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('uraian', 255);
            $table->decimal('anggaran_dialokasi', 15, 2)->default(0);
            $table->decimal('realisasi', 15, 2)->default(0);
            $table->decimal('sisa', 15, 2)->default(0);
            $table->decimal('persentase_realisasi', 5, 2)->default(0);
            $table->enum('periode', ['triwulan_1', 'triwulan_2', 'triwulan_3', 'triwulan_4', 'semester_1', 'semester_2', 'tahunan']);
            $table->integer('tahun');
            $table->enum('status', ['draft', 'disetujui', 'aktif', 'selesai']);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'tahun', 'status']);
            $table->index(['periode', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_categories', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori_keu', 20)->unique();
            $table->string('nama_kategori', 100);
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->enum('parent_type', ['induk', 'sub']);
            $table->foreignId('parent_id')->nullable()->constrained('financial_categories')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tipe', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_categories');
    }
};

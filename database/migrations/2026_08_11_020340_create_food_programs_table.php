<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_programs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_program', 30)->unique();
            $table->string('nama_program', 200);
            $table->text('deskripsi')->nullable();
            $table->enum('jenis_program', ['pertanian', 'peternakan', 'perikanan', 'integrated', 'pengolahan']);
            $table->enum('status', ['perencanaan', 'aktif', 'selesai', 'dibatalkan'])->default('perencanaan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_rencana_selesai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->decimal('realisasi_anggaran', 15, 2)->default(0);
            $table->integer('target_peserta')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'status']);
            $table->index(['jenis_program', 'status']);
            $table->index('tanggal_mulai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_programs');
    }
};

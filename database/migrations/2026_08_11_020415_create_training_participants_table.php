<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained()->onDelete('cascade');
            $table->foreignId('wbp_id')->constrained('wbp')->onDelete('cascade');
            $table->date('tanggal_daftar');
            $table->enum('status', ['terdaftar', 'hadir', 'tidak_hadir', 'selesai', 'drop_out'])->default('terdaftar');
            $table->decimal('nilai_akhir', 5, 2)->nullable()->comment('0-100');
            $table->boolean('lulus')->default(false);
            $table->boolean('sertifikat_dikeluarkan')->default(false);
            $table->string('nomor_sertifikat', 50)->nullable();
            $table->date('tanggal_sertifikat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['training_program_id', 'wbp_id']);
            $table->index(['wbp_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_participants');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_health_records', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kesehatan', 30)->unique();
            $table->foreignId('barn_id')->constrained('livestock_barns')->onDelete('cascade');
            $table->enum('jenis_pemeriksaan', ['vaksinasi', 'pengobatan', 'inspeksi', 'karantina', 'pencegahan']);
            $table->date('tanggal_pemeriksaan');
            $table->foreignId('wbp_pic_id')->nullable()->constrained('wbp')->onDelete('set null');
            $table->decimal('biaya', 12, 2)->default(0);
            $table->enum('hasil', ['sehat', 'sakit', 'perlu_perawatan', 'karantina', 'mati']);
            $table->text('diagnosa')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['barn_id', 'tanggal_pemeriksaan']);
            $table->index(['jenis_pemeriksaan', 'hasil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_health_records');
    }
};

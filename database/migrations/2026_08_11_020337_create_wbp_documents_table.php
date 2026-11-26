<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wbp_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wbp_id')->constrained('wbp')->onDelete('cascade');
            $table->string('jenis_dokumen', 50)->comment('KTP, SK, Putusan, dll');
            $table->string('nomor_dokumen', 100)->nullable();
            $table->string('nama_dokumen', 255);
            $table->string('file_path', 500);
            $table->date('tanggal_dokumen')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['wbp_id', 'jenis_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wbp_documents');
    }
};

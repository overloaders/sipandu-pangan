<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('kode_blok', 20);
            $table->string('nama_blok', 100);
            $table->enum('jenis_blok', ['umum', 'khusus', 'isolasi', 'maximum', 'terapi', 'wanita', 'anak']);
            $table->integer('kapasitas')->default(0);
            $table->integer('terisi')->default(0);
            $table->enum('status', ['aktif', 'renovasi', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['unit_id', 'kode_blok']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};

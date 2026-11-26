<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained()->onDelete('cascade');
            $table->string('kode_kamar', 20);
            $table->string('nama_kamar', 100);
            $table->integer('kapasitas')->default(0);
            $table->integer('terisi')->default(0);
            $table->enum('status', ['tersedia', 'penuh', 'renovasi', 'nonaktif'])->default('tersedia');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['block_id', 'kode_kamar']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

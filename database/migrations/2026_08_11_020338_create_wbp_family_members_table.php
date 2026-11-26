<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wbp_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wbp_id')->constrained('wbp')->onDelete('cascade');
            $table->string('nama_lengkap', 150);
            $table->enum('hubungan', ['suami', 'istri', 'ayah', 'ibu', 'anak', 'saudara', 'lainnya']);
            $table->string('nik', 20)->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('is_wali')->default(false);
            $table->timestamps();

            $table->index(['wbp_id', 'hubungan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wbp_family_members');
    }
};

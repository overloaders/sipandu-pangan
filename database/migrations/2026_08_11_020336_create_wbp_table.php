<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wbp', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_induk', 30)->unique()->comment('Nomor Induk WBP');
            $table->string('nama_lengkap', 150);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir', 100);
            $table->string('nik', 20)->unique()->comment('NIK KTP');
            $table->string('agama', 20);
            $table->string('kebangsaan', 50)->default('Indonesia');
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->string('pekerjaan_sebelum', 100)->nullable();
            $table->text('alamat_asal')->nullable();
            $table->string('kota_asal', 100)->nullable();
            $table->string('provinsi_asal', 100)->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('sidik_jari', 255)->nullable()->comment('Path file sidik jari');
            $table->enum('status', ['aktif', 'dikeluarkan', 'pindah', 'meninggal', 'bebas'])->default('aktif');
            $table->enum('jenis_pidana', ['pidana_umum', 'pidana_khusus', 'narkotika'])->nullable();
            $table->text('kasus')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_beberapa')->nullable()->comment('Perkiraan tanggal bebas');
            $table->date('masa_hukuman_selesai')->nullable();
            $table->integer('masa_hukuman_hari')->nullable()->comment('Total hari hukuman');
            $table->integer('remisi_hari')->default(0)->comment('Total remisi dalam hari');
            $table->string('nomor_register_a', 30)->nullable();
            $table->string('nomor_register_b', 30)->nullable();
            $table->string('nomor_register_c', 30)->nullable();
            $table->string('nomor_register_d', 30)->nullable();
            $table->string('nomor_register_e', 30)->nullable();
            $table->string('nomor_register_f', 30)->nullable();
            $table->string('nomor_register_g', 30)->nullable();
            $table->string('nomor_register_h', 30)->nullable();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('block_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->string('tamping', 50)->nullable()->comment('Jenis tamping');
            $table->enum('kelompok_masa_tahanan', ['pendek', 'menengah', 'panjang'])->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('tanggal_masuk');
            $table->index(['unit_id', 'status']);
            $table->index(['block_id', 'room_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wbp');
    }
};

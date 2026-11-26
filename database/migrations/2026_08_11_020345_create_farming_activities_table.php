<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farming_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farming_plan_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_kegiatan', ['pengolahan_tanah', 'penanaman', 'pemupukan', 'penyiraman', 'penyiangan', 'pengendalian_hama', 'pemangkasan', 'panen', 'pasca_panen', 'lainnya']);
            $table->date('tanggal_kegiatan');
            $table->foreignId('pic_wbp_id')->nullable()->constrained('wbp')->onDelete('set null');
            $table->decimal('biaya', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamps();

            $table->index(['farming_plan_id', 'jenis_kegiatan']);
            $table->index('tanggal_kegiatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farming_activities');
    }
};

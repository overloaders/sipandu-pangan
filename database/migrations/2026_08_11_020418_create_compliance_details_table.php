<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compliance_id')->constrained('compliance_records')->onDelete('cascade');
            $table->string('item_pemeriksaan', 255);
            $table->enum('status_pemeriksaan', ['memenuhi', 'tidak_memenuhi', 'sebagian', 'tidak_diterapkan']);
            $table->text('catatan')->nullable();
            $table->string('bukti_dukung', 255)->nullable();
            $table->integer('bobot')->default(1)->comment('Bobot penilaian');
            $table->integer('nilai')->nullable()->comment('Nilai aktual');
            $table->timestamps();

            $table->index(['compliance_id', 'status_pemeriksaan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_details');
    }
};

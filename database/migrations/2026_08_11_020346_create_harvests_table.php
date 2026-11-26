<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_panen', 30)->unique();
            $table->foreignId('farming_plan_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_panen');
            $table->decimal('total_berat_kg', 10, 2);
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->enum('kualitas', ['A', 'B', 'C', 'D']);
            $table->decimal('harga_jual_per_kg', 12, 2);
            $table->text('keterangan')->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamps();

            $table->index(['farming_plan_id', 'tanggal_panen']);
            $table->index('tanggal_panen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('kode_opname', 30)->unique();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_opname');
            $table->enum('jenis_opname', ['rutin', 'insidental', 'penutupan_buku']);
            $table->enum('status', ['draft', 'proses', 'selesai', 'dibatalkan'])->default('draft');
            $table->foreignId('petugas_wbp_id')->nullable()->constrained('wbp')->onDelete('set null');
            $table->foreignId('verifikator_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('total_item_checked')->default(0);
            $table->integer('item_selisih')->default(0);
            $table->decimal('total_selisih_nilai', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['unit_id', 'status']);
            $table->index(['tanggal_opname', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};

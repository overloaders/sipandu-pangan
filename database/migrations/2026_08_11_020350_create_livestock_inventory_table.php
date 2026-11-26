<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ternak', 30)->unique();
            $table->foreignId('barn_id')->constrained('livestock_barns')->onDelete('cascade');
            $table->foreignId('livestock_type_id')->constrained()->onDelete('cascade');
            $table->integer('jumlah')->default(0);
            $table->decimal('berat_rata_kg', 8, 2)->default(0);
            $table->date('tanggal_masuk');
            $table->enum('asal', ['pembelian', 'breeding', 'hibah', 'konversi']);
            $table->decimal('harga_pokok', 12, 2)->default(0);
            $table->enum('status', ['hidup', 'mati', 'dijual', 'dipanen', 'sakit']);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['barn_id', 'status']);
            $table->index(['livestock_type_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_inventory');
    }
};

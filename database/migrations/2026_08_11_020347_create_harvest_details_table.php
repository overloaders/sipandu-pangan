<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvest_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('harvest_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_type_id')->constrained()->onDelete('cascade');
            $table->decimal('berat_kg', 10, 2);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('subtotal', 15, 2);
            $table->string('tujuan', 50)->comment('konsumsi_wbp, dijual, disimpan');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['harvest_id', 'crop_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvest_details');
    }
};

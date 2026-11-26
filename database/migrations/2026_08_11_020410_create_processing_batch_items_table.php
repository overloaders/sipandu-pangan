<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processing_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('processing_batches')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('jumlah_digunakan', 10, 2);
            $table->string('satuan', 30)->default('kg');
            $table->decimal('nilai_bahan', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['batch_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processing_batch_items');
    }
};

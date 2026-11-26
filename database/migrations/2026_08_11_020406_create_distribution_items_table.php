<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('jumlah', 12, 2);
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('satuan', 30)->default('kg');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['distribution_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_items');
    }
};

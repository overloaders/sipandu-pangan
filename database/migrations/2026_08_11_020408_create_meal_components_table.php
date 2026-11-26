<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nutrition_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('jumlah', 8, 2);
            $table->string('satuan', 30)->default('gram');
            $table->decimal('kalori_kcal', 8, 2)->nullable();
            $table->decimal('protein_gr', 8, 2)->nullable();
            $table->decimal('karbohidrat_gr', 8, 2)->nullable();
            $table->decimal('lemak_gr', 8, 2)->nullable();
            $table->integer('urutan')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['nutrition_plan_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_components');
    }
};

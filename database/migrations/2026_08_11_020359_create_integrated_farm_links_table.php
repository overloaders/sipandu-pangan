<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrated_farm_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_program_id')->constrained()->onDelete('cascade');
            $table->foreignId('land_plot_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('barn_id')->nullable()->constrained('livestock_barns')->onDelete('set null');
            $table->foreignId('pond_id')->nullable()->constrained('fish_ponds')->onDelete('set null');
            $table->enum('tipe_integration', ['land_to_barn', 'barn_to_pond', 'pond_to_land', 'full_cycle', 'custom']);
            $table->enum('status', ['aktif', 'nonaktif']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index(['food_program_id', 'tipe_integration']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrated_farm_links');
    }
};

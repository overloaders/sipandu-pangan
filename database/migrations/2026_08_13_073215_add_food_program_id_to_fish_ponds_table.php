<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fish_ponds', function (Blueprint $table) {
            $table->foreignId('food_program_id')->nullable()->after('unit_id')->constrained('food_programs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fish_ponds', function (Blueprint $table) {
            $table->dropForeign(['food_program_id']);
            $table->dropColumn('food_program_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // farming_plans → get unit_id from food_programs
        Schema::table('farming_plans', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->onDelete('cascade');
        });

        // harvests → get unit_id from farming_plans
        Schema::table('harvests', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->onDelete('cascade');
        });

        // livestock_inventory → get unit_id from livestock_barns
        Schema::table('livestock_inventory', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->onDelete('cascade');
        });

        // fish_stock → get unit_id from fish_ponds
        Schema::table('fish_stock', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->onDelete('cascade');
        });

        // Seed existing data: populate unit_id from related tables
        DB::statement('
            UPDATE farming_plans fp
            JOIN food_programs fprog ON fp.food_program_id = fprog.id
            SET fp.unit_id = fprog.unit_id
            WHERE fp.unit_id IS NULL AND fprog.unit_id IS NOT NULL
        ');

        DB::statement('
            UPDATE harvests h
            JOIN farming_plans fp ON h.farming_plan_id = fp.id
            SET h.unit_id = fp.unit_id
            WHERE h.unit_id IS NULL AND fp.unit_id IS NOT NULL
        ');

        DB::statement('
            UPDATE livestock_inventory li
            JOIN livestock_barns lb ON li.barn_id = lb.id
            SET li.unit_id = lb.unit_id
            WHERE li.unit_id IS NULL AND lb.unit_id IS NOT NULL
        ');

        DB::statement('
            UPDATE fish_stock fs
            JOIN fish_ponds fp ON fs.pond_id = fp.id
            SET fs.unit_id = fp.unit_id
            WHERE fs.unit_id IS NULL AND fp.unit_id IS NOT NULL
        ');

        // Add indexes
        Schema::table('farming_plans', function (Blueprint $table) {
            $table->index('unit_id');
        });
        Schema::table('harvests', function (Blueprint $table) {
            $table->index('unit_id');
        });
        Schema::table('livestock_inventory', function (Blueprint $table) {
            $table->index('unit_id');
        });
        Schema::table('fish_stock', function (Blueprint $table) {
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('farming_plans', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropIndex(['unit_id']);
            $table->dropColumn('unit_id');
        });
        Schema::table('harvests', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropIndex(['unit_id']);
            $table->dropColumn('unit_id');
        });
        Schema::table('livestock_inventory', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropIndex(['unit_id']);
            $table->dropColumn('unit_id');
        });
        Schema::table('fish_stock', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropIndex(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};

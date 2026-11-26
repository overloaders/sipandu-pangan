<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan string kosong menjadi NULL agar unique index valid
        DB::table('wbp_family_members')->where('nik', '')->update(['nik' => null]);

        Schema::table('wbp_family_members', function (Blueprint $table) {
            $table->unique('nik');
        });
    }

    public function down(): void
    {
        Schema::table('wbp_family_members', function (Blueprint $table) {
            $table->dropUnique(['nik']);
        });
    }
};

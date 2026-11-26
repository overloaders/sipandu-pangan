<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * file_path boleh kosong — tidak semua dokumen punya file scan
 * (entry manual via halaman detail WBP).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wbp_documents', function (Blueprint $table) {
            $table->string('file_path', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('wbp_documents', function (Blueprint $table) {
            $table->string('file_path', 500)->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan index standalone untuk kolom tanggal yang dipakai
 * filter/sort global (tanpa prefix parent/unit) di laporan & dashboard:
 *  - daily_reports.tanggal_laporan        (filter rentang tanggal laporan harian)
 *  - livestock_health_records.tanggal_pemeriksaan (stat "pemeriksaan minggu ini")
 *  - compliance_records.tanggal_pemeriksaan       (sort & ringkasan laporan sertifikasi)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->index('tanggal_laporan');
        });
        Schema::table('livestock_health_records', function (Blueprint $table) {
            $table->index('tanggal_pemeriksaan');
        });
        Schema::table('compliance_records', function (Blueprint $table) {
            $table->index('tanggal_pemeriksaan');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropIndex(['tanggal_laporan']);
        });
        Schema::table('livestock_health_records', function (Blueprint $table) {
            $table->dropIndex(['tanggal_pemeriksaan']);
        });
        Schema::table('compliance_records', function (Blueprint $table) {
            $table->dropIndex(['tanggal_pemeriksaan']);
        });
    }
};

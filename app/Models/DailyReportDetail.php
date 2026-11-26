<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportDetail extends Model
{
    protected $fillable = [
        'daily_report_id', 'kategori_laporan', 'judul_detail', 'deskripsi',
        'nilai_angka', 'satuan', 'foto_pendukung',
    ];

    public function dailyReport() { return $this->belongsTo(DailyReport::class); }
}

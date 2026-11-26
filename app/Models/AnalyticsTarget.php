<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsTarget extends Model
{
    protected $fillable = [
        'indicator_id', 'unit_id', 'periode_tahun', 'periode_bulan',
        'periode_triwulan', 'target_nilai', 'realisasi_nilai',
        'persentase_capaian', 'status_capaian', 'catatan',
    ];

    protected $casts = [
        'periode_tahun' => 'integer',
        'periode_bulan' => 'integer',
        'periode_triwulan' => 'integer',
        'target_nilai' => 'decimal:2',
        'realisasi_nilai' => 'decimal:2',
        'persentase_capaian' => 'decimal:2',
    ];

    public function indicator() { return $this->belongsTo(AnalyticsIndicator::class, 'indicator_id'); }
    public function unit() { return $this->belongsTo(Unit::class); }
}

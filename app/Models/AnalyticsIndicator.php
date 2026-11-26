<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalyticsIndicator extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_indikator', 'nama_indikator', 'kategori', 'deskripsi',
        'rumus_perhitungan', 'satuan', 'target_nilai', 'batas_minimum',
        'batas_maksimum', 'unit_id', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function targets() { return $this->hasMany(AnalyticsTarget::class, 'indicator_id'); }
}

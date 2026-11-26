<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterQualityRecord extends Model
{
    protected $fillable = [
        'pond_id', 'tanggal_pemeriksaan', 'jam_pemeriksaan', 'suhu_celsius',
        'ph', 'dissolved_oxygen_mgl', 'amonia_mgl', 'nitrit_mgl', 'nitrat_mgl',
        'kekeruhan_ntu', 'salinitas_ppt', 'kondisi_air', 'tindakan_perlukan', 'pemeriksa_id',
    ];

    protected $casts = ['tanggal_pemeriksaan' => 'date'];

    public function pond() { return $this->belongsTo(FishPond::class, 'pond_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmingActivity extends Model
{
    protected $fillable = [
        'farming_plan_id', 'jenis_kegiatan', 'tanggal_kegiatan',
        'pic_wbp_id', 'biaya', 'keterangan', 'foto',
    ];

    protected $casts = ['tanggal_kegiatan' => 'date', 'biaya' => 'decimal:2'];

    public function farmingPlan() { return $this->belongsTo(FarmingPlan::class); }
    public function picWbp() { return $this->belongsTo(Wbp::class, 'pic_wbp_id'); }
}

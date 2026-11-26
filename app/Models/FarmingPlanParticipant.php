<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmingPlanParticipant extends Model
{
    protected $fillable = [
        'farming_plan_id', 'wbp_id', 'tanggal_mulai', 'tanggal_selesai',
        'status', 'peran', 'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function farmingPlan() { return $this->belongsTo(FarmingPlan::class); }
    public function wbp() { return $this->belongsTo(Wbp::class); }
}

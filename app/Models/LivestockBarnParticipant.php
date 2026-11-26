<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockBarnParticipant extends Model
{
    protected $fillable = [
        'livestock_barn_id', 'wbp_id', 'tanggal_mulai', 'tanggal_selesai',
        'status', 'peran', 'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function livestockBarn() { return $this->belongsTo(LivestockBarn::class); }
    public function wbp() { return $this->belongsTo(Wbp::class); }
}

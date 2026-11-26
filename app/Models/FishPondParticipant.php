<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FishPondParticipant extends Model
{
    protected $fillable = [
        'fish_pond_id', 'wbp_id', 'tanggal_mulai', 'tanggal_selesai',
        'status', 'peran', 'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function fishPond() { return $this->belongsTo(FishPond::class); }
    public function wbp() { return $this->belongsTo(Wbp::class); }
}

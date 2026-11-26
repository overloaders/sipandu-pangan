<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockHealthRecord extends Model
{
    protected $fillable = [
        'kode_kesehatan', 'barn_id', 'jenis_pemeriksaan', 'tanggal_pemeriksaan',
        'wbp_pic_id', 'biaya', 'hasil', 'diagnosa', 'tindakan', 'keterangan',
    ];

    protected $casts = ['tanggal_pemeriksaan' => 'date', 'biaya' => 'decimal:2'];

    public function barn() { return $this->belongsTo(LivestockBarn::class, 'barn_id'); }
    public function picWbp() { return $this->belongsTo(Wbp::class, 'wbp_pic_id'); }
    public function details() { return $this->hasMany(LivestockHealthDetail::class, 'health_record_id'); }
}

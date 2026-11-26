<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    protected $fillable = [
        'kode_panen', 'unit_id', 'farming_plan_id', 'tanggal_panen', 'total_berat_kg',
        'total_pendapatan', 'kualitas', 'harga_jual_per_kg', 'keterangan', 'foto',
    ];

    protected $casts = [
        'tanggal_panen' => 'date',
        'total_berat_kg' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
        'harga_jual_per_kg' => 'decimal:2',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function farmingPlan() { return $this->belongsTo(FarmingPlan::class); }
    public function details() { return $this->hasMany(HarvestDetail::class); }
}

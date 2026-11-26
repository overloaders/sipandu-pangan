<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropType extends Model
{
    protected $fillable = [
        'kode_tanaman', 'nama_tanaman', 'nama_latin', 'kategori',
        'masa_tanam_hari', 'hasil_per_m2_kg', 'harga_jual_per_kg',
        'musim_tanam', 'keterangan',
    ];

    protected $casts = ['masa_tanam_hari' => 'integer'];

    public function farmingPlans() { return $this->hasMany(FarmingPlan::class); }
    public function harvestDetails() { return $this->hasMany(HarvestDetail::class); }
}

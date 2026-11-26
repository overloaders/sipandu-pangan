<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestDetail extends Model
{
    protected $fillable = [
        'harvest_id', 'crop_type_id', 'berat_kg', 'harga_per_kg', 'subtotal', 'tujuan', 'keterangan',
    ];

    protected $casts = ['berat_kg' => 'decimal:2', 'harga_per_kg' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function harvest() { return $this->belongsTo(Harvest::class); }
    public function cropType() { return $this->belongsTo(CropType::class); }
}

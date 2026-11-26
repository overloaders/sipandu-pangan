<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockInventory extends Model
{
    use SoftDeletes;

    protected $table = 'livestock_inventory';

    protected $fillable = [
        'kode_ternak', 'unit_id', 'barn_id', 'livestock_type_id', 'jumlah',
        'berat_rata_kg', 'tanggal_masuk', 'asal', 'harga_pokok', 'status', 'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'berat_rata_kg' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'harga_pokok' => 'decimal:2',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function barn() { return $this->belongsTo(LivestockBarn::class, 'barn_id'); }
    public function livestockType() { return $this->belongsTo(LivestockType::class); }
    public function healthDetails() { return $this->hasMany(LivestockHealthDetail::class); }
}

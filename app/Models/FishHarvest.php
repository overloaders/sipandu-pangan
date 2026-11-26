<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FishHarvest extends Model
{
    protected $fillable = [
        'kode_panen_ikan', 'fish_stock_id', 'pond_id', 'tanggal_panen',
        'jumlah_dipanen', 'total_berat_kg', 'ukuran_rata_cm', 'harga_jual_per_kg',
        'total_pendapatan', 'total_biaya', 'kualitas', 'tujuan', 'keterangan',
    ];

    protected $casts = [
        'tanggal_panen' => 'date',
        'jumlah_dipanen' => 'integer',
        'total_berat_kg' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
    ];

    public function fishStock() { return $this->belongsTo(FishStock::class); }
    public function pond() { return $this->belongsTo(FishPond::class, 'pond_id'); }
}

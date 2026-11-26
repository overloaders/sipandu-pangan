<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FishStock extends Model
{
    use SoftDeletes;

    protected $table = 'fish_stock';

    protected $fillable = [
        'kode_stok_ikan', 'unit_id', 'pond_id', 'jenis_ikan', 'kategori', 'jumlah',
        'ukuran_rata_cm', 'berat_rata_gram', 'tanggal_tebur', 'harga_benih',
        'total_biaya_pakan', 'status', 'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tanggal_tebur' => 'date',
        'harga_benih' => 'decimal:2',
        'total_biaya_pakan' => 'decimal:2',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function pond() { return $this->belongsTo(FishPond::class, 'pond_id'); }
    public function fishHarvests() { return $this->hasMany(FishHarvest::class); }
}

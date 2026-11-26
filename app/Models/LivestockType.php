<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockType extends Model
{
    protected $fillable = [
        'kode_jenis', 'nama_jenis', 'kategori', 'berat_rata_kg',
        'harga_beli_per_ekor', 'harga_jual_per_kg', 'masa_pemeliharaan_hari',
        'pakan_utama', 'keterangan',
    ];

    protected $casts = ['masa_pemeliharaan_hari' => 'integer'];

    public function inventories() { return $this->hasMany(LivestockInventory::class); }
}

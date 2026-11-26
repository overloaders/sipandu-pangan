<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedSupply extends Model
{
    use SoftDeletes;

    protected $table = 'feed_supplies';

    protected $fillable = [
        'kode_pakan', 'nama_pakan', 'jenis_pakan', 'satuan', 'stok',
        'stok_minimum', 'harga_per_satuan', 'supplier', 'tanggal_kadaluarsa', 'status',
    ];

    protected $casts = ['stok' => 'decimal:2', 'stok_minimum' => 'decimal:2', 'tanggal_kadaluarsa' => 'date'];

    public function feedLogs() { return $this->hasMany(FeedLog::class); }
}

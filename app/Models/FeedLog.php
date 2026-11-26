<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedLog extends Model
{
    protected $fillable = [
        'barn_id', 'feed_supply_id', 'wbp_id', 'tanggal_pemberian',
        'waktu_pemberian', 'jumlah', 'satuan', 'biaya', 'keterangan',
    ];

    protected $casts = ['tanggal_pemberian' => 'date', 'jumlah' => 'decimal:2', 'biaya' => 'decimal:2'];

    public function barn() { return $this->belongsTo(LivestockBarn::class, 'barn_id'); }
    public function feedSupply() { return $this->belongsTo(FeedSupply::class); }
    public function wbp() { return $this->belongsTo(Wbp::class); }
}

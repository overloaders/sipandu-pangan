<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    protected $fillable = [
        'distribution_id', 'item_id', 'jumlah', 'harga_satuan', 'subtotal', 'satuan', 'keterangan',
    ];

    protected $casts = ['jumlah' => 'decimal:2', 'harga_satuan' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function distribution() { return $this->belongsTo(Distribution::class); }
    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }
}

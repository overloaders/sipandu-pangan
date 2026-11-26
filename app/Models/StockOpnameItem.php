<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    protected $fillable = [
        'opname_id', 'item_id', 'stok_sistem', 'stok_fisik', 'selisih', 'nilai_selisih', 'keterangan',
    ];

    protected $casts = ['stok_sistem' => 'decimal:2', 'stok_fisik' => 'decimal:2', 'selisih' => 'decimal:2', 'nilai_selisih' => 'decimal:2'];

    public function opname() { return $this->belongsTo(StockOpname::class, 'opname_id'); }
    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }
}

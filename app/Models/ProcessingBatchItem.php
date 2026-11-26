<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingBatchItem extends Model
{
    protected $fillable = [
        'batch_id', 'item_id', 'jumlah_digunakan', 'satuan', 'nilai_bahan', 'keterangan',
    ];

    protected $casts = ['jumlah_digunakan' => 'decimal:2', 'nilai_bahan' => 'decimal:2'];

    public function batch() { return $this->belongsTo(ProcessingBatch::class, 'batch_id'); }
    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }
}

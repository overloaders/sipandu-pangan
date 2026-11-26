<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'kode_transaksi', 'item_id', 'tipe_transaksi', 'jumlah', 'harga_satuan',
        'total_nilai', 'sumber', 'ref_unit_id', 'created_by_user_id',
        'tanggal_transaksi', 'bukti_transaksi', 'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_nilai' => 'decimal:2',
        'tanggal_transaksi' => 'date',
    ];

    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function refUnit() { return $this->belongsTo(Unit::class, 'ref_unit_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by_user_id'); }
}

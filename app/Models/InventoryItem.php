<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $table = 'inventory_items';

    protected $fillable = [
        'kode_item', 'nama_item', 'category_id', 'unit_id', 'satuan',
        'stok_saat_ini', 'stok_minimum', 'stok_maksimum', 'harga_beli_rata',
        'harga_jual', 'tanggal_kadaluarsa', 'tipe_pangan', 'asal', 'status',
        'lokasi_simpan', 'foto', 'keterangan',
    ];

    protected $casts = [
        'stok_saat_ini' => 'decimal:2',
        'stok_minimum' => 'decimal:2',
        'stok_maksimum' => 'decimal:2',
        'harga_beli_rata' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'tanggal_kadaluarsa' => 'date',
    ];

    public function category() { return $this->belongsTo(InventoryCategory::class, 'category_id'); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class, 'item_id'); }
    public function mealComponents() { return $this->hasMany(MealComponent::class); }
    public function processingBatchItems() { return $this->hasMany(ProcessingBatchItem::class); }
    public function statusHistories() { return $this->hasMany(InventoryItemStatusHistory::class, 'inventory_item_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($item) {
            if ($item->status) {
                InventoryItemStatusHistory::create([
                    'inventory_item_id' => $item->id,
                    'status_lama' => null,
                    'status_baru' => $item->status,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        static::updated(function ($item) {
            $old = $item->getOriginal('status');
            $new = $item->status;
            if ($old !== $new) {
                InventoryItemStatusHistory::create([
                    'inventory_item_id' => $item->id,
                    'status_lama' => $old,
                    'status_baru' => $new,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        // Hapus riwayat saat item dihapus (soft delete) — konsisten dgn pola cascade
        static::deleted(function ($item) {
            $item->statusHistories()->delete();
        });
    }
}

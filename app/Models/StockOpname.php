<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_opname', 'unit_id', 'tanggal_opname', 'jenis_opname', 'status',
        'petugas_wbp_id', 'verifikator_user_id', 'total_item_checked',
        'item_selisih', 'total_selisih_nilai', 'keterangan',
    ];

    protected $casts = ['tanggal_opname' => 'date', 'total_selisih_nilai' => 'decimal:2'];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function petugasWbp() { return $this->belongsTo(Wbp::class, 'petugas_wbp_id'); }
    public function verifikator() { return $this->belongsTo(User::class, 'verifikator_user_id'); }
    public function items() { return $this->hasMany(StockOpnameItem::class, 'opname_id'); }

    public function statusHistories() { return $this->hasMany(StockOpnameStatusHistory::class, 'stock_opname_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                StockOpnameStatusHistory::create([
                    'stock_opname_id' => $model->id,
                    'status_lama' => null,
                    'status_baru' => $model->status,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        static::updated(function ($model) {
            $old = $model->getOriginal('status');
            $new = $model->status;
            if ($old !== $new) {
                StockOpnameStatusHistory::create([
                    'stock_opname_id' => $model->id,
                    'status_lama' => $old,
                    'status_baru' => $new,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        // Hapus riwayat saat record dihapus (soft delete) — konsisten dgn pola cascade
        static::deleted(function ($model) {
            $model->statusHistories()->delete();
        });
    }
}

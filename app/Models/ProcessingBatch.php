<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessingBatch extends Model
{
    use SoftDeletes;

    protected $table = 'processing_batches';

    protected $fillable = [
        'kode_batch', 'nama_produk', 'unit_id', 'jenis_pengolahan',
        'tanggal_produksi', 'tanggal_kadaluarsa', 'jumlah_produksi',
        'satuan_output', 'biaya_produksi', 'harga_jual_satuan',
        'status', 'pic_wbp_id', 'keterangan',
    ];

    protected $casts = [
        'tanggal_produksi' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'jumlah_produksi' => 'decimal:2',
        'biaya_produksi' => 'decimal:2',
        'harga_jual_satuan' => 'decimal:2',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function picWbp() { return $this->belongsTo(Wbp::class, 'pic_wbp_id'); }
    public function items() { return $this->hasMany(ProcessingBatchItem::class, 'batch_id'); }

    public function statusHistories() { return $this->hasMany(ProcessingBatchStatusHistory::class, 'processing_batch_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                ProcessingBatchStatusHistory::create([
                    'processing_batch_id' => $model->id,
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
                ProcessingBatchStatusHistory::create([
                    'processing_batch_id' => $model->id,
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

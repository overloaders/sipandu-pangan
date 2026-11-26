<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distribution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_distribusi', 'tipe_distribusi', 'unit_id', 'tanggal_distribusi',
        'total_nilai', 'jumlah_penerima', 'penerima_nama', 'penerima_kontak',
        'alamat_penerima', 'status', 'created_by_user_id', 'keterangan',
    ];

    protected $casts = [
        'tanggal_distribusi' => 'date',
        'total_nilai' => 'decimal:2',
        'jumlah_penerima' => 'integer',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function items() { return $this->hasMany(DistributionItem::class); }
    public function foodProgram() { return $this->belongsTo(FoodProgram::class); }

    public function statusHistories() { return $this->hasMany(DistributionStatusHistory::class, 'distribution_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                DistributionStatusHistory::create([
                    'distribution_id' => $model->id,
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
                DistributionStatusHistory::create([
                    'distribution_id' => $model->id,
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

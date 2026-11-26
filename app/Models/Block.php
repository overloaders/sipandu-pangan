<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'unit_id', 'kode_blok', 'nama_blok', 'jenis_blok',
        'kapasitas', 'terisi', 'status', 'keterangan',
    ];

    protected function casts(): array
    {
        return ['kapasitas' => 'integer', 'terisi' => 'integer'];
    }

    public function unit() { return $this->belongsTo(Unit::class); }
    public function rooms() { return $this->hasMany(Room::class); }
    public function wbps() { return $this->hasMany(Wbp::class); }
    public function roomAssignments() { return $this->hasMany(RoomAssignment::class); }

    public function statusHistories() { return $this->hasMany(BlockStatusHistory::class, 'block_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                BlockStatusHistory::create([
                    'block_id' => $model->id,
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
                BlockStatusHistory::create([
                    'block_id' => $model->id,
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

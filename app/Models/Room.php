<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'block_id', 'kode_kamar', 'nama_kamar', 'kapasitas',
        'terisi', 'status', 'keterangan',
    ];

    protected function casts(): array
    {
        return ['kapasitas' => 'integer', 'terisi' => 'integer'];
    }

    public function block() { return $this->belongsTo(Block::class); }
    public function wbps() { return $this->hasMany(Wbp::class); }
    public function roomAssignments() { return $this->hasMany(RoomAssignment::class); }

    public function statusHistories() { return $this->hasMany(RoomStatusHistory::class, 'room_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                RoomStatusHistory::create([
                    'room_id' => $model->id,
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
                RoomStatusHistory::create([
                    'room_id' => $model->id,
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

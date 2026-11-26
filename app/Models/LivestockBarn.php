<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockBarn extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $table = 'livestock_barns';

    protected $fillable = [
        'kode_kandang', 'nama_kandang', 'unit_id', 'food_program_id', 'jenis_ternak',
        'kapasitas', 'terisi', 'luas_m2', 'tipe_kandang', 'status', 'keterangan',
    ];

    protected $casts = ['kapasitas' => 'integer', 'terisi' => 'integer', 'luas_m2' => 'decimal:2'];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function foodProgram() { return $this->belongsTo(FoodProgram::class); }
    public function inventories() { return $this->hasMany(LivestockInventory::class, 'barn_id'); }
    public function feedLogs() { return $this->hasMany(FeedLog::class, 'barn_id'); }
    public function healthRecords() { return $this->hasMany(LivestockHealthRecord::class, 'barn_id'); }
    public function participants() { return $this->hasMany(LivestockBarnParticipant::class); }
    public function integratedLinks() { return $this->hasMany(IntegratedFarmLink::class, 'barn_id'); }

    public function statusHistories() { return $this->hasMany(LivestockBarnStatusHistory::class, 'livestock_barn_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                LivestockBarnStatusHistory::create([
                    'livestock_barn_id' => $model->id,
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
                LivestockBarnStatusHistory::create([
                    'livestock_barn_id' => $model->id,
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

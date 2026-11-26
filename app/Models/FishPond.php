<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FishPond extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $table = 'fish_ponds';

    protected $fillable = [
        'kode_kolam', 'nama_kolam', 'unit_id', 'food_program_id', 'luas_m2', 'kedalaman_m',
        'volume_m3', 'tipe_kolam', 'sumber_air', 'status', 'keterangan',
    ];

    protected $casts = ['luas_m2' => 'decimal:2', 'volume_m3' => 'decimal:2'];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function foodProgram() { return $this->belongsTo(FoodProgram::class); }
    public function fishStocks() { return $this->hasMany(FishStock::class, 'pond_id'); }
    public function waterQualityRecords() { return $this->hasMany(WaterQualityRecord::class, 'pond_id'); }
    public function fishHarvests() { return $this->hasMany(FishHarvest::class, 'pond_id'); }
    public function participants() { return $this->hasMany(FishPondParticipant::class); }
    public function integratedLinks() { return $this->hasMany(IntegratedFarmLink::class, 'pond_id'); }

    public function statusHistories() { return $this->hasMany(FishPondStatusHistory::class, 'fish_pond_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                FishPondStatusHistory::create([
                    'fish_pond_id' => $model->id,
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
                FishPondStatusHistory::create([
                    'fish_pond_id' => $model->id,
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

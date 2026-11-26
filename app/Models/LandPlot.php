<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandPlot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_lahan', 'nama_lahan', 'unit_id', 'luas_m2', 'jenis_tanah',
        'sistem_irigasi', 'status', 'latitude', 'longitude', 'keterangan',
    ];

    protected $casts = ['luas_m2' => 'decimal:2', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7'];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function farmingPlans() { return $this->hasMany(FarmingPlan::class); }
    public function integratedLinks() { return $this->hasMany(IntegratedFarmLink::class); }

    public function statusHistories() { return $this->hasMany(LandPlotStatusHistory::class, 'land_plot_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                LandPlotStatusHistory::create([
                    'land_plot_id' => $model->id,
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
                LandPlotStatusHistory::create([
                    'land_plot_id' => $model->id,
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

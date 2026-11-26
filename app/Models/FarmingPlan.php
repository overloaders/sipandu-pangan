<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FarmingPlan extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'kode_rencana', 'unit_id', 'food_program_id', 'land_plot_id', 'crop_type_id',
        'jumlah_tanaman', 'tanggal_tanam_rencana', 'tanggal_tanam_aktual',
        'tanggal_panen_rencana', 'tanggal_panen_aktual', 'luas_tanam_m2',
        'metode_budi_daya', 'status', 'estimasi_hasil_kg', 'estimasi_pendapatan', 'keterangan',
    ];

    protected $casts = [
        'jumlah_tanaman' => 'integer',
        'tanggal_tanam_rencana' => 'date',
        'tanggal_tanam_aktual' => 'date',
        'tanggal_panen_rencana' => 'date',
        'tanggal_panen_aktual' => 'date',
        'luas_tanam_m2' => 'decimal:2',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function foodProgram() { return $this->belongsTo(FoodProgram::class); }
    public function landPlot() { return $this->belongsTo(LandPlot::class); }
    public function cropType() { return $this->belongsTo(CropType::class); }
    public function activities() { return $this->hasMany(FarmingActivity::class); }
    public function harvests() { return $this->hasMany(Harvest::class); }
    public function participants() { return $this->hasMany(FarmingPlanParticipant::class); }

    public function statusHistories() { return $this->hasMany(FarmingPlanStatusHistory::class, 'farming_plan_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                FarmingPlanStatusHistory::create([
                    'farming_plan_id' => $model->id,
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
                FarmingPlanStatusHistory::create([
                    'farming_plan_id' => $model->id,
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

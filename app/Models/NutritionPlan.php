<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NutritionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_rencana_gizi', 'nama_menu', 'unit_id', 'waktu_makan',
        'berlaku_mulai', 'berlaku_selesai', 'kalori_target_kcal',
        'protein_target_gr', 'karbohidrat_target_gr', 'lemak_target_gr',
        'serat_target_gr', 'tipe_porsi', 'estimasi_biaya_per_porsi',
        'sumber_bahan', 'deskripsi', 'status',
    ];

    protected $casts = [
        'berlaku_mulai' => 'date',
        'berlaku_selesai' => 'date',
        'estimasi_biaya_per_porsi' => 'decimal:2',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function components() { return $this->hasMany(MealComponent::class); }

    public function statusHistories() { return $this->hasMany(NutritionPlanStatusHistory::class, 'nutrition_plan_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                NutritionPlanStatusHistory::create([
                    'nutrition_plan_id' => $model->id,
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
                NutritionPlanStatusHistory::create([
                    'nutrition_plan_id' => $model->id,
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

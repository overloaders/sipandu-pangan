<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodProgram extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'kode_program', 'nama_program', 'deskripsi', 'jenis_program',
        'status', 'tanggal_mulai', 'tanggal_rencana_selesai', 'tanggal_selesai',
        'unit_id', 'created_by', 'anggaran', 'realisasi_anggaran',
        'target_peserta', 'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_rencana_selesai' => 'date',
        'tanggal_selesai' => 'date',
        'anggaran' => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
        'target_peserta' => 'integer',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function participants() { return $this->hasMany(ProgramParticipant::class); }
    public function farmingPlans() { return $this->hasMany(FarmingPlan::class); }
    public function livestockBarns() { return $this->hasMany(LivestockBarn::class); }
    public function fishPonds() { return $this->hasMany(FishPond::class); }
    public function integratedLinks() { return $this->hasMany(IntegratedFarmLink::class); }
    public function budgets() { return $this->hasMany(Budget::class); }

    public function scopeActive($query) { return $query->where('status', 'aktif'); }
    public function scopeByType($query, string $type) { return $query->where('jenis_program', $type); }

    /**
     * Hitung total peserta dari semua rencana kerja
     */
    public function getTotalParticipantsAttribute(): int
    {
        $farm = $this->farmingPlans->sum(fn($fp) => $fp->participants->count());
        $live = $this->livestockBarns->sum(fn($b) => $b->participants->count());
        $fish = $this->fishPonds->sum(fn($p) => $p->participants->count());
        return $farm + $live + $fish;
    }

    public function statusHistories() { return $this->hasMany(FoodProgramStatusHistory::class, 'food_program_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                FoodProgramStatusHistory::create([
                    'food_program_id' => $model->id,
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
                FoodProgramStatusHistory::create([
                    'food_program_id' => $model->id,
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

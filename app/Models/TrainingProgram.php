<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgram extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_pelatihan', 'nama_pelatihan', 'deskripsi', 'kategori', 'unit_id',
        'instructor_user_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_pelatihan',
        'kapasitas_peserta', 'biaya', 'lokasi', 'tipe', 'status',
        'sertifikat_diberikan', 'materi_pokok',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jam_pelatihan' => 'integer',
        'kapasitas_peserta' => 'integer',
        'biaya' => 'decimal:2',
        'sertifikat_diberikan' => 'boolean',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function instructor() { return $this->belongsTo(User::class, 'instructor_user_id'); }
    public function participants() { return $this->hasMany(TrainingParticipant::class); }
    public function certifications() { return $this->hasMany(TrainingCertification::class); }

    public function statusHistories() { return $this->hasMany(TrainingProgramStatusHistory::class, 'training_program_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                TrainingProgramStatusHistory::create([
                    'training_program_id' => $model->id,
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
                TrainingProgramStatusHistory::create([
                    'training_program_id' => $model->id,
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

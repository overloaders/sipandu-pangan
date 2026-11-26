<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TrainingParticipant extends Model
{
    protected $fillable = [
        'training_program_id', 'wbp_id', 'tanggal_daftar', 'status',
        'nilai_akhir', 'lulus', 'sertifikat_dikeluarkan', 'nomor_sertifikat',
        'tanggal_sertifikat', 'catatan',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'nilai_akhir' => 'decimal:2',
        'lulus' => 'boolean',
        'sertifikat_dikeluarkan' => 'boolean',
        'tanggal_sertifikat' => 'date',
    ];

    public function trainingProgram() { return $this->belongsTo(TrainingProgram::class); }
    public function wbp() { return $this->belongsTo(Wbp::class); }
    public function certification() {
        return $this->hasOne(TrainingCertification::class, 'wbp_id', 'wbp_id');
    }

    public function certificationForProgram($programId) {
        return $this->hasOne(TrainingCertification::class, 'wbp_id', 'wbp_id')
            ->where('training_program_id', $programId);
    }
}

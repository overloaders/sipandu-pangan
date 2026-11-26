<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCertification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_sertifikat', 'wbp_id', 'training_program_id', 'nama_sertifikat',
        'nomor_sertifikat_resmi', 'tanggal_terbit', 'masa_berlaku',
        'lembaga_sertifikasi', 'tingkat', 'file_sertifikat', 'status', 'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'masa_berlaku' => 'date',
    ];

    public function wbp() { return $this->belongsTo(Wbp::class); }
    public function trainingProgram() { return $this->belongsTo(TrainingProgram::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceRecord extends Model
{
    use SoftDeletes;

    protected $table = 'compliance_records';

    protected $fillable = [
        'kode_komplians', 'judul', 'tipe', 'unit_id', 'tanggal_pemeriksaan',
        'tanggal_berlaku_sampai', 'hasil', 'nilai_skor', 'tingkat_risiko',
        'inspektur_user_id', 'status', 'temuan', 'rekomendasi', 'dokumen_pendukung',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'tanggal_berlaku_sampai' => 'date',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function inspektur() { return $this->belongsTo(User::class, 'inspektur_user_id'); }
    public function details() { return $this->hasMany(ComplianceDetail::class, 'compliance_id'); }
    public function statusHistories() { return $this->hasMany(ComplianceRecordStatusHistory::class, 'compliance_record_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($record) {
            if ($record->status) {
                ComplianceRecordStatusHistory::create([
                    'compliance_record_id' => $record->id,
                    'status_lama' => null,
                    'status_baru' => $record->status,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        static::updated(function ($record) {
            $old = $record->getOriginal('status');
            $new = $record->status;
            if ($old !== $new) {
                ComplianceRecordStatusHistory::create([
                    'compliance_record_id' => $record->id,
                    'status_lama' => $old,
                    'status_baru' => $new,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        // Hapus riwayat saat record dihapus (soft delete) — konsisten dgn pola cascade
        static::deleted(function ($record) {
            $record->statusHistories()->delete();
        });
    }
}

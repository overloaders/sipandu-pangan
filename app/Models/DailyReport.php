<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReport extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'kode_laporan', 'unit_id', 'tanggal_laporan', 'dilaporkan_oleh',
        'dikonfirmasi_oleh', 'status', 'total_wbp_aktif', 'total_wbp_baru_masuk',
        'total_wbp_keluar', 'total_panen_hari_ini_kg', 'total_pakan_hari_ini_kg',
        'total_pendapatan_hari_ini', 'total_pengeluaran_hari_ini',
        'ringkasan_kegiatan', 'catatan_khusus', 'masalah_ditemukan',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'total_wbp_aktif' => 'integer',
        'total_panen_hari_ini_kg' => 'decimal:2',
        'total_pendapatan_hari_ini' => 'decimal:2',
        'total_pengeluaran_hari_ini' => 'decimal:2',
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function reporter() { return $this->belongsTo(User::class, 'dilaporkan_oleh'); }
    public function confirmer() { return $this->belongsTo(User::class, 'dikonfirmasi_oleh'); }
    public function details() { return $this->hasMany(DailyReportDetail::class); }

    public function statusHistories() { return $this->hasMany(DailyReportStatusHistory::class, 'daily_report_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if ($model->status) {
                DailyReportStatusHistory::create([
                    'daily_report_id' => $model->id,
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
                DailyReportStatusHistory::create([
                    'daily_report_id' => $model->id,
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

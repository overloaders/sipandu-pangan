<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wbp extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $table = 'wbp';

    protected $fillable = [
        'nomor_induk', 'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir',
        'tempat_lahir', 'nik', 'agama', 'kebangsaan', 'pendidikan_terakhir',
        'pekerjaan_sebelum', 'alamat_asal', 'kota_asal', 'provinsi_asal',
        'nomor_hp', 'foto', 'sidik_jari', 'status', 'jenis_pidana', 'kasus',
        'tanggal_masuk', 'tanggal_beberapa', 'masa_hukuman_selesai',
        'masa_hukuman_hari', 'remisi_hari',
        'nomor_register_a', 'nomor_register_b', 'nomor_register_c',
        'nomor_register_d', 'nomor_register_e', 'nomor_register_f',
        'nomor_register_g', 'nomor_register_h',
        'unit_id', 'block_id', 'room_id', 'tamping',
        'kelompok_masa_tahanan', 'keterangan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_beberapa' => 'date',
        'masa_hukuman_selesai' => 'date',
        'masa_hukuman_hari' => 'integer',
        'remisi_hari' => 'integer',
    ];

    // Relationships
    public function unit() { return $this->belongsTo(Unit::class); }
    public function block() { return $this->belongsTo(Block::class); }
    public function room() { return $this->belongsTo(Room::class); }
    public function documents() { return $this->hasMany(WbpDocument::class); }
    public function familyMembers() { return $this->hasMany(WbpFamilyMember::class); }
    public function roomAssignments() { return $this->hasMany(RoomAssignment::class); }
    public function programParticipations() { return $this->hasMany(ProgramParticipant::class); }
    public function trainingParticipations() { return $this->hasMany(TrainingParticipant::class); }
    public function certifications() { return $this->hasMany(TrainingCertification::class); }

    // Scopes
    public function scopeActive($query) { return $query->where('status', 'aktif'); }
    public function scopeSearch($query, string $term) {
        return $query->where(function ($q) use ($term) {
            $q->where('nomor_induk', 'LIKE', "%{$term}%")
              ->orWhere('nama_lengkap', 'LIKE', "%{$term}%")
              ->orWhere('nik', 'LIKE', "%{$term}%");
        });
    }

    public function statusHistories() { return $this->hasMany(WbpStatusHistory::class, 'wbp_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($wbp) {
            if ($wbp->status) {
                WbpStatusHistory::create([
                    'wbp_id' => $wbp->id,
                    'status_lama' => null,
                    'status_baru' => $wbp->status,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        static::updated(function ($wbp) {
            $old = $wbp->getOriginal('status');
            $new = $wbp->status;
            if ($old !== $new) {
                WbpStatusHistory::create([
                    'wbp_id' => $wbp->id,
                    'status_lama' => $old,
                    'status_baru' => $new,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        // Hapus riwayat saat record dihapus (soft delete) — konsisten dgn pola cascade
        static::deleted(function ($wbp) {
            $wbp->statusHistories()->delete();
        });
    }
}

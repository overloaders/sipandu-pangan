<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WbpFamilyMember extends Model
{
    protected $fillable = [
        'wbp_id', 'nama_lengkap', 'hubungan', 'nik', 'nomor_hp', 'alamat', 'is_wali',
    ];

    protected $casts = ['is_wali' => 'boolean'];

    public function wbp() { return $this->belongsTo(Wbp::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceDetail extends Model
{
    protected $fillable = [
        'compliance_id', 'item_pemeriksaan', 'status_pemeriksaan', 'catatan',
        'bukti_dukung', 'bobot', 'nilai',
    ];

    protected $casts = ['bobot' => 'integer', 'nilai' => 'integer'];

    public function compliance() { return $this->belongsTo(ComplianceRecord::class, 'compliance_id'); }
}

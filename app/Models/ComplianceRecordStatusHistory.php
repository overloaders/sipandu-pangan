<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceRecordStatusHistory extends Model
{
    protected $fillable = [
        'compliance_record_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function record()
    {
        return $this->belongsTo(ComplianceRecord::class, 'compliance_record_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

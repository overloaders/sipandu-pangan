<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportStatusHistory extends Model
{
    protected $fillable = [
        'daily_report_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

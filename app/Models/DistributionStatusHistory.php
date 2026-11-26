<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionStatusHistory extends Model
{
    protected $fillable = [
        'distribution_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function distribution()
    {
        return $this->belongsTo(Distribution::class, 'distribution_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

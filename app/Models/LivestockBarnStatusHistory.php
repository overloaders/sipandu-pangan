<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockBarnStatusHistory extends Model
{
    protected $fillable = [
        'livestock_barn_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function livestockBarn()
    {
        return $this->belongsTo(LivestockBarn::class, 'livestock_barn_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

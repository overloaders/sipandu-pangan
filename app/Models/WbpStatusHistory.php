<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WbpStatusHistory extends Model
{
    protected $fillable = [
        'wbp_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function wbp()
    {
        return $this->belongsTo(Wbp::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

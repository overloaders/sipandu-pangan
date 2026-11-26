<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FishPondStatusHistory extends Model
{
    protected $fillable = [
        'fish_pond_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function fishPond()
    {
        return $this->belongsTo(FishPond::class, 'fish_pond_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

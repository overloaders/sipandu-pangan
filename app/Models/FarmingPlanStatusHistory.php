<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmingPlanStatusHistory extends Model
{
    protected $fillable = [
        'farming_plan_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function farmingPlan()
    {
        return $this->belongsTo(FarmingPlan::class, 'farming_plan_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

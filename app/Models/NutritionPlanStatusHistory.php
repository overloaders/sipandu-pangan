<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionPlanStatusHistory extends Model
{
    protected $fillable = [
        'nutrition_plan_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function nutritionPlan()
    {
        return $this->belongsTo(NutritionPlan::class, 'nutrition_plan_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

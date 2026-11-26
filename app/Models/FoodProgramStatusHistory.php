<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodProgramStatusHistory extends Model
{
    protected $fillable = [
        'food_program_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function foodProgram()
    {
        return $this->belongsTo(FoodProgram::class, 'food_program_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

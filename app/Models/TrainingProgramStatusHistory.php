<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingProgramStatusHistory extends Model
{
    protected $fillable = [
        'training_program_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

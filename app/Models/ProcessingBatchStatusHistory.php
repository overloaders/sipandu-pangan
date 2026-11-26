<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingBatchStatusHistory extends Model
{
    protected $fillable = [
        'processing_batch_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function processingBatch()
    {
        return $this->belongsTo(ProcessingBatch::class, 'processing_batch_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

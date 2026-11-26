<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockStatusHistory extends Model
{
    protected $fillable = [
        'block_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

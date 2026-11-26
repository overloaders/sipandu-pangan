<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomStatusHistory extends Model
{
    protected $fillable = [
        'room_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    protected $fillable = [
        'wbp_id', 'room_id', 'block_id', 'tanggal_mulai', 'tanggal_selesai', 'alasan', 'keterangan',
    ];

    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date'];

    public function wbp() { return $this->belongsTo(Wbp::class); }
    public function room() { return $this->belongsTo(Room::class); }
    public function block() { return $this->belongsTo(Block::class); }
}

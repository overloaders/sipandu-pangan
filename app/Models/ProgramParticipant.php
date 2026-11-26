<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramParticipant extends Model
{
    protected $fillable = [
        'food_program_id', 'wbp_id', 'tanggal_mulai', 'tanggal_selesai', 'status', 'peran', 'keterangan',
    ];

    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date'];

    public function foodProgram() { return $this->belongsTo(FoodProgram::class); }
    public function wbp() { return $this->belongsTo(Wbp::class); }
}

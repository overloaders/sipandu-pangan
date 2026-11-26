<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockHealthDetail extends Model
{
    protected $fillable = [
        'health_record_id', 'livestock_inventory_id', 'obat_diberikan',
        'dosis', 'jadwal_ulang', 'catatan',
    ];

    protected $casts = ['jadwal_ulang' => 'date'];

    public function healthRecord() { return $this->belongsTo(LivestockHealthRecord::class, 'health_record_id'); }
    public function livestockInventory() { return $this->belongsTo(LivestockInventory::class); }
}

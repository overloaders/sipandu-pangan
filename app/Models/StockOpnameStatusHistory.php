<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameStatusHistory extends Model
{
    protected $fillable = [
        'stock_opname_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

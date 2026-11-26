<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItemStatusHistory extends Model
{
    protected $fillable = [
        'inventory_item_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

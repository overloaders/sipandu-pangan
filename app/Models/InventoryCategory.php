<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['kode_kategori', 'nama_kategori', 'tipe', 'satuan_standar', 'keterangan'];

    public function items() { return $this->hasMany(InventoryItem::class, 'category_id'); }
}

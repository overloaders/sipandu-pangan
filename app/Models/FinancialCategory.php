<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['kode_kategori_keu', 'nama_kategori', 'tipe', 'parent_type', 'parent_id', 'keterangan', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function parent() { return $this->belongsTo(FinancialCategory::class, 'parent_id'); }
    public function children() { return $this->hasMany(FinancialCategory::class, 'parent_id'); }
    public function transactions() { return $this->hasMany(FinancialTransaction::class, 'category_id'); }
}

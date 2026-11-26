<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_anggaran', 'food_program_id', 'category_id', 'unit_id',
        'uraian', 'anggaran_dialokasi', 'realisasi', 'sisa',
        'persentase_realisasi', 'periode', 'tahun', 'status',
    ];

    protected $casts = [
        'anggaran_dialokasi' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'sisa' => 'decimal:2',
        'persentase_realisasi' => 'decimal:2',
        'tahun' => 'integer',
    ];

    public function foodProgram() { return $this->belongsTo(FoodProgram::class); }
    public function category() { return $this->belongsTo(FinancialCategory::class, 'category_id'); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function transactions() { return $this->hasMany(FinancialTransaction::class); }
}

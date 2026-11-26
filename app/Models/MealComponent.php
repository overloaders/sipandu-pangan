<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealComponent extends Model
{
    protected $fillable = [
        'nutrition_plan_id', 'item_id', 'jumlah', 'satuan', 'kalori_kcal',
        'protein_gr', 'karbohidrat_gr', 'lemak_gr', 'urutan', 'keterangan',
    ];

    protected $casts = ['urutan' => 'integer'];

    public function nutritionPlan() { return $this->belongsTo(NutritionPlan::class); }
    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }
}

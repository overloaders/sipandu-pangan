<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegratedFarmLink extends Model
{
    protected $fillable = [
        'food_program_id', 'land_plot_id', 'barn_id', 'pond_id',
        'tipe_integration', 'status', 'deskripsi',
    ];

    public function foodProgram() { return $this->belongsTo(FoodProgram::class); }
    public function landPlot() { return $this->belongsTo(LandPlot::class); }
    public function barn() { return $this->belongsTo(LivestockBarn::class, 'barn_id'); }
    public function pond() { return $this->belongsTo(FishPond::class, 'pond_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandPlotStatusHistory extends Model
{
    protected $fillable = [
        'land_plot_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    public function landPlot()
    {
        return $this->belongsTo(LandPlot::class, 'land_plot_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

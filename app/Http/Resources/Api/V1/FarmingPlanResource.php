<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmingPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_rencana' => $this->kode_rencana,
            'jumlah_tanaman' => $this->jumlah_tanaman,
            'tanggal_tanam_rencana' => $this->tanggal_tanam_rencana?->format('Y-m-d'),
            'tanggal_tanam_aktual' => $this->tanggal_tanam_aktual?->format('Y-m-d'),
            'luas_tanam_m2' => $this->luas_tanam_m2,
            'metode_budi_daya' => $this->metode_budi_daya,
            'estimasi_hasil_kg' => $this->estimasi_hasil_kg,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            
            'food_program' => new FoodProgramResource($this->whenLoaded('foodProgram')),
            'land_plot' => new LandPlotResource($this->whenLoaded('landPlot')),
            'crop_type' => new CropTypeResource($this->whenLoaded('cropType')),
            'activities' => FarmingActivityResource::collection($this->whenLoaded('activities')),
            'harvests' => HarvestResource::collection($this->whenLoaded('harvests')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

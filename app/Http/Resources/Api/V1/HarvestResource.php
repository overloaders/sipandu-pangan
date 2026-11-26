<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HarvestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_panen' => $this->kode_panen,
            'tanggal_panen' => $this->tanggal_panen?->format('Y-m-d'),
            'total_berat_kg' => $this->total_berat_kg,
            'kualitas' => $this->kualitas,
            'total_pendapatan' => $this->total_pendapatan,
            'keterangan' => $this->keterangan,
            
            'farming_plan' => new FarmingPlanResource($this->whenLoaded('farmingPlan')),
            'harvest_details' => HarvestDetailResource::collection($this->whenLoaded('harvestDetails')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

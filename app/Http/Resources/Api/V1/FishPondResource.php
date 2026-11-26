<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FishPondResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_kolam' => $this->kode_kolam,
            'nama_kolam' => $this->nama_kolam,
            'luas_m2' => $this->luas_m2,
            'tipe_kolam' => $this->tipe_kolam,
            'sumber_air' => $this->sumber_air,
            'status' => $this->status,
            
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'fish_stocks' => FishStockResource::collection($this->whenLoaded('fishStocks')),
            'water_quality_records' => WaterQualityRecordResource::collection($this->whenLoaded('waterQualityRecords')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

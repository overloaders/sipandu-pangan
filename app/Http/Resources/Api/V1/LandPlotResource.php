<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandPlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_lahan' => $this->kode_lahan,
            'nama_lahan' => $this->nama_lahan,
            'luas_m2' => $this->luas_m2,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            
            'unit' => new UnitResource($this->whenLoaded('unit')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

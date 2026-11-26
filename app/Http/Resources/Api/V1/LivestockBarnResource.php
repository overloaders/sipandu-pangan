<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestockBarnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_kandang' => $this->kode_kandang,
            'nama_kandang' => $this->nama_kandang,
            'jenis_ternak' => $this->jenis_ternak,
            'kapasitas' => $this->kapasitas,
            'terisi' => $this->terisi,
            'luas_m2' => $this->luas_m2,
            'tipe_kandang' => $this->tipe_kandang,
            'status' => $this->status,
            
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'inventories' => LivestockInventoryResource::collection($this->whenLoaded('inventories')),
            'health_records' => LivestockHealthRecordResource::collection($this->whenLoaded('healthRecords')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

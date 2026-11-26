<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaterQualityRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan?->format('Y-m-d H:i:s'),
            'ph' => $this->ph,
            'suhu_celsius' => $this->suhu_celsius,
            'oksigen_terlarut' => $this->oksigen_terlarut,
            'amonia' => $this->amonia,
            'kekeruhan' => $this->kekeruhan,
            'kondisi_air' => $this->kondisi_air,
            'keterangan' => $this->keterangan,
            
            'pond' => new FishPondResource($this->whenLoaded('pond')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

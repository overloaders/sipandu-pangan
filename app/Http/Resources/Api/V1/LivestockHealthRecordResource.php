<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestockHealthRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan?->format('Y-m-d'),
            'jenis_pemeriksaan' => $this->jenis_pemeriksaan,
            'hasil' => $this->hasil,
            'tindakan' => $this->tindakan,
            'keterangan' => $this->keterangan,
            
            'barn' => new LivestockBarnResource($this->whenLoaded('barn')),
            'health_details' => LivestockHealthDetailResource::collection($this->whenLoaded('healthDetails')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

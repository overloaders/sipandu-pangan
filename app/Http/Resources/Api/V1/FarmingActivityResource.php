<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmingActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'jenis_kegiatan' => $this->jenis_kegiatan,
            'tanggal_kegiatan' => $this->tanggal_kegiatan?->format('Y-m-d'),
            'deskripsi' => $this->deskripsi,
            'biaya' => $this->biaya,
            'keterangan' => $this->keterangan,
            
            'pic_wbp' => new WbpResource($this->whenLoaded('picWbp')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

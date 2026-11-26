<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_kamar' => $this->kode_kamar,
            'nama_kamar' => $this->nama_kamar,
            'kapasitas' => $this->kapasitas,
            'terisi' => $this->terisi,
            'status' => $this->status,
            
            'block' => new BlockResource($this->whenLoaded('block')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

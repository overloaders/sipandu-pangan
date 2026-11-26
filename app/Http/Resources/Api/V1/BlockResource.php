<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_blok' => $this->kode_blok,
            'nama_blok' => $this->nama_blok,
            'jenis_blok' => $this->jenis_blok,
            'kapasitas' => $this->kapasitas,
            'terisi' => $this->terisi,
            'status' => $this->status,
            
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

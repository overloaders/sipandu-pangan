<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestockInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_ternak' => $this->kode_ternak,
            'jumlah' => $this->jumlah,
            'berat_rata_kg' => $this->berat_rata_kg,
            'tanggal_masuk' => $this->tanggal_masuk?->format('Y-m-d'),
            'asal' => $this->asal,
            'harga_pokok' => $this->harga_pokok,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            
            'barn' => new LivestockBarnResource($this->whenLoaded('barn')),
            'livestock_type' => new LivestockTypeResource($this->whenLoaded('livestockType')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

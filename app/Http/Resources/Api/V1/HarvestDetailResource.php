<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HarvestDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'jenis_panen' => $this->jenis_panen,
            'berat_kg' => $this->berat_kg,
            'jumlah_unit' => $this->jumlah_unit,
            'harga_per_unit' => $this->harga_per_unit,
            'total_nilai' => $this->total_nilai,
            'keterangan' => $this->keterangan,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

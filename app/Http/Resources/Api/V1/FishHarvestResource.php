<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FishHarvestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_panen_ikan' => $this->kode_panen_ikan,
            'tanggal_panen' => $this->tanggal_panen?->format('Y-m-d'),
            'jumlah' => $this->jumlah,
            'berat_total_kg' => $this->berat_total_kg,
            'harga_jual_per_kg' => $this->harga_jual_per_kg,
            'total_pendapatan' => $this->total_pendapatan,
            'keterangan' => $this->keterangan,
            
            'pond' => new FishPondResource($this->whenLoaded('pond')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

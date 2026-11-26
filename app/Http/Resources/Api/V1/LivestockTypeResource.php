<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestockTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_jenis' => $this->kode_jenis,
            'nama_jenis' => $this->nama_jenis,
            'kategori' => $this->kategori,
            'masa_penyimpanan_hari' => $this->masa_penyimpanan_hari,
            'target_berat_kg' => $this->target_berat_kg,
            'keterangan' => $this->keterangan,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

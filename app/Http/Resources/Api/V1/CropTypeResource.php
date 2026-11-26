<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CropTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_tanaman' => $this->kode_tanaman,
            'nama_tanaman' => $this->nama_tanaman,
            'nama_latin' => $this->nama_latin,
            'kategori' => $this->kategori,
            'masa_tanam_hari' => $this->masa_tanam_hari,
            'hasil_per_m2_kg' => $this->hasil_per_m2_kg,
            'harga_jual_per_kg' => $this->harga_jual_per_kg,
            'musim_tanam' => $this->musim_tanam,
            'keterangan' => $this->keterangan,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

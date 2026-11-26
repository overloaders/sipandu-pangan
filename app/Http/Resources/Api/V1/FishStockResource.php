<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FishStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_stok_ikan' => $this->kode_stok_ikan,
            'jenis_ikan' => $this->jenis_ikan,
            'kategori' => $this->kategori,
            'jumlah' => $this->jumlah,
            'berat_rata_gram' => $this->berat_rata_gram,
            'tanggal_tebar' => $this->tanggal_tebar?->format('Y-m-d'),
            'sumber_benih' => $this->sumber_benih,
            'harga_benih' => $this->harga_benih,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            
            'pond' => new FishPondResource($this->whenLoaded('pond')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

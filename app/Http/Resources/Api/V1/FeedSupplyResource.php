<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedSupplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_pakan' => $this->kode_pakan,
            'nama_pakan' => $this->nama_pakan,
            'jenis_pakan' => $this->jenis_pakan,
            'satuan' => $this->satuan,
            'stok' => $this->stok,
            'stok_minimum' => $this->stok_minimum,
            'harga_per_satuan' => $this->harga_per_satuan,
            'supplier' => $this->supplier,
            'tanggal_kadaluarsa' => $this->tanggal_kadaluarsa?->toDateString(),
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

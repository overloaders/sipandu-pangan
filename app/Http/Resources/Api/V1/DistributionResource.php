<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_distribusi' => $this->kode_distribusi,
            'tipe_distribusi' => $this->tipe_distribusi,
            'unit_id' => $this->unit_id,
            'tanggal_distribusi' => $this->tanggal_distribusi?->toDateString(),
            'total_nilai' => $this->total_nilai,
            'jumlah_penerima' => $this->jumlah_penerima,
            'penerima_nama' => $this->penerima_nama,
            'penerima_kontak' => $this->penerima_kontak,
            'alamat_penerima' => $this->alamat_penerima,
            'status' => $this->status,
            'created_by_user_id' => $this->created_by_user_id,
            'keterangan' => $this->keterangan,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'items' => DistributionItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

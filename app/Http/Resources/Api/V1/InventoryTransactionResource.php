<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_transaksi' => $this->kode_transaksi,
            'tipe_transaksi' => $this->tipe_transaksi,
            'jumlah' => $this->jumlah,
            'sumber' => $this->sumber,
            'harga_satuan' => $this->harga_satuan,
            'total_nilai' => $this->total_nilai,
            'tanggal_transaksi' => $this->tanggal_transaksi?->format('Y-m-d'),
            'keterangan' => $this->keterangan,
            
            'item' => new InventoryItemResource($this->whenLoaded('item')),
            'ref_unit' => new UnitResource($this->whenLoaded('refUnit')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

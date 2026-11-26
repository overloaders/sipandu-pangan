<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_item' => $this->kode_item,
            'nama_item' => $this->nama_item,
            'satuan' => $this->satuan,
            'stok_saat_ini' => $this->stok_saat_ini,
            'stok_minimum' => $this->stok_minimum,
            'stok_maksimum' => $this->stok_maksimum,
            'harga_beli_rata' => $this->harga_beli_rata,
            'harga_jual' => $this->harga_jual,
            'tipe_pangan' => $this->tipe_pangan,
            'asal' => $this->asal,
            'status' => $this->status,
            'nilai_stok' => $this->stok_saat_ini * $this->harga_beli_rata,
            'is_low_stock' => $this->stok_saat_ini < $this->stok_minimum,
            
            'category' => new InventoryCategoryResource($this->whenLoaded('category')),
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'transactions' => InventoryTransactionResource::collection($this->whenLoaded('transactions')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

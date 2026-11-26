<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'distribution_id' => $this->distribution_id,
            'item_id' => $this->item_id,
            'jumlah' => $this->jumlah,
            'harga_satuan' => $this->harga_satuan,
            'subtotal' => $this->subtotal,
            'satuan' => $this->satuan,
            'keterangan' => $this->keterangan,
            'item' => new InventoryItemResource($this->whenLoaded('item')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

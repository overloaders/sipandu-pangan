<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'barn_id' => $this->barn_id,
            'feed_supply_id' => $this->feed_supply_id,
            'wbp_id' => $this->wbp_id,
            'tanggal_pemberian' => $this->tanggal_pemberian?->toDateString(),
            'waktu_pemberian' => $this->waktu_pemberian,
            'jumlah' => $this->jumlah,
            'satuan' => $this->satuan,
            'biaya' => $this->biaya,
            'keterangan' => $this->keterangan,
            'barn' => new LivestockBarnResource($this->whenLoaded('barn')),
            'feedSupply' => new FeedSupplyResource($this->whenLoaded('feedSupply')),
            'wbp' => new WbpResource($this->whenLoaded('wbp')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

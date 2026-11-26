<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestockHealthDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'livestock_inventory_id' => $this->livestock_inventory_id,
            'diagnosa' => $this->diagnosa,
            'obat' => $this->obat,
            'dosis' => $this->dosis,
            'biaya' => $this->biaya,
            'keterangan' => $this->keterangan,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

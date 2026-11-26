<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplianceDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'compliance_id' => $this->compliance_id,
            'item_pemeriksaan' => $this->item_pemeriksaan,
            'status_pemeriksaan' => $this->status_pemeriksaan,
            'catatan' => $this->catatan,
            'bukti_dukung' => $this->bukti_dukung,
            'bobot' => $this->bobot,
            'nilai' => $this->nilai,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

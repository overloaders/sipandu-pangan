<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'indicator_id' => $this->indicator_id,
            'unit_id' => $this->unit_id,
            'periode_tahun' => $this->periode_tahun,
            'periode_bulan' => $this->periode_bulan,
            'periode_triwulan' => $this->periode_triwulan,
            'target_nilai' => $this->target_nilai,
            'realisasi_nilai' => $this->realisasi_nilai,
            'persentase_capaian' => $this->persentase_capaian,
            'status_capaian' => $this->status_capaian,
            'catatan' => $this->catatan,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsIndicatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_indikator' => $this->kode_indikator,
            'nama_indikator' => $this->nama_indikator,
            'deskripsi' => $this->deskripsi,
            'kategori' => $this->kategori,
            'satuan' => $this->satuan,
            'target_nilai' => $this->target_nilai,
            'is_active' => $this->is_active,
            'targets' => TargetResource::collection($this->whenLoaded('targets')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_anggaran' => $this->kode_anggaran,
            'food_program_id' => $this->food_program_id,
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'uraian' => $this->uraian,
            'anggaran_dialokasi' => $this->anggaran_dialokasi,
            'realisasi' => $this->realisasi,
            'sisa' => $this->sisa,
            'persentase_realisasi' => $this->persentase_realisasi,
            'periode' => $this->periode,
            'tahun' => $this->tahun,
            'status' => $this->status,
            'food_program' => new FoodProgramResource($this->whenLoaded('foodProgram')),
            'category' => new FinancialCategoryResource($this->whenLoaded('category')),
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NutritionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_rencana_gizi' => $this->kode_rencana_gizi,
            'nama_menu' => $this->nama_menu,
            'unit_id' => $this->unit_id,
            'waktu_makan' => $this->waktu_makan,
            'berlaku_mulai' => $this->berlaku_mulai?->toDateString(),
            'berlaku_selesai' => $this->berlaku_selesai?->toDateString(),
            'kalori_target_kcal' => $this->kalori_target_kcal,
            'protein_target_gr' => $this->protein_target_gr,
            'karbohidrat_target_gr' => $this->karbohidrat_target_gr,
            'lemak_target_gr' => $this->lemak_target_gr,
            'serat_target_gr' => $this->serat_target_gr,
            'tipe_porsi' => $this->tipe_porsi,
            'estimasi_biaya_per_porsi' => $this->estimasi_biaya_per_porsi,
            'sumber_bahan' => $this->sumber_bahan,
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

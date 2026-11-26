<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_program' => $this->kode_program,
            'nama_program' => $this->nama_program,
            'deskripsi' => $this->deskripsi,
            'jenis_program' => $this->jenis_program,
            'status' => $this->status,
            'tanggal_mulai' => $this->tanggal_mulai?->format('Y-m-d'),
            'tanggal_rencana_selesai' => $this->tanggal_rencana_selesai?->format('Y-m-d'),
            'anggaran' => $this->anggaran,
            'realisasi_anggaran' => $this->realisasi_anggaran,
            'target_peserta' => $this->total_participants,
            'total_penerima' => $this->total_penerima,
            'sumber_anggaran' => $this->sumber_anggaran,
            
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'participants' => ProgramParticipantResource::collection($this->whenLoaded('participants')),
            'farming_plans' => FarmingPlanResource::collection($this->whenLoaded('farmingPlans')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

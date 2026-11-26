<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tanggal_daftar' => $this->tanggal_daftar?->format('Y-m-d'),
            'status_kehadiran' => $this->status_kehadiran,
            'nilai' => $this->nilai,
            'status_kelulusan' => $this->status_kelulusan,
            'keterangan' => $this->keterangan,
            
            'wbp' => new WbpResource($this->whenLoaded('wbp')),
            'training_program' => new TrainingProgramResource($this->whenLoaded('trainingProgram')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

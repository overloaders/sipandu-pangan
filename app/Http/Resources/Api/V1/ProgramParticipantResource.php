<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'peran' => $this->peran,
            'tanggal_bergabung' => $this->tanggal_bergabung?->format('Y-m-d'),
            'tanggal_selesai' => $this->tanggal_selesai?->format('Y-m-d'),
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            
            'wbp' => new WbpResource($this->whenLoaded('wbp')),
            'food_program' => new FoodProgramResource($this->whenLoaded('foodProgram')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

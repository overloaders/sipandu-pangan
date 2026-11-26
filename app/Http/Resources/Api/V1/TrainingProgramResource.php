<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_pelatihan' => $this->kode_pelatihan,
            'nama_pelatihan' => $this->nama_pelatihan,
            'deskripsi' => $this->deskripsi,
            'kategori' => $this->kategori,
            'tanggal_mulai' => $this->tanggal_mulai?->format('Y-m-d'),
            'tanggal_selesai' => $this->tanggal_selesai?->format('Y-m-d'),
            'lokasi' => $this->lokasi,
            'instruktur' => $this->instruktur,
            'kapasitas' => $this->kapasitas,
            'status' => $this->status,
            
            'participants' => TrainingParticipantResource::collection($this->whenLoaded('participants')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WbpFamilyMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_lengkap' => $this->nama_lengkap,
            'hubungan' => $this->hubungan,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'pekerjaan' => $this->pekerjaan,
            'nomor_hp' => $this->nomor_hp,
            'alamat' => $this->alamat,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

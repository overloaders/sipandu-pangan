<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WbpResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_induk' => $this->nomor_induk,
            'nama_lengkap' => $this->nama_lengkap,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'usia' => $this->tanggal_lahir?->age,
            'tempat_lahir' => $this->tempat_lahir,
            'nik' => $this->nik,
            'agama' => $this->agama,
            'pendidikan_terakhir' => $this->pendidikan_terakhir,
            'pekerjaan_sebelum' => $this->pekerjaan_sebelum,
            'alamat_asal' => $this->alamat_asal,
            'kota_asal' => $this->kota_asal,
            'provinsi_asal' => $this->provinsi_asal,
            'nomor_hp' => $this->nomor_hp,
            'jenis_pidana' => $this->jenis_pidana,
            'kasus' => $this->kasus,
            'tanggal_masuk' => $this->tanggal_masuk?->format('Y-m-d'),
            'masa_hukuman_hari' => $this->masa_hukuman_hari,
            'masa_hukuman_selesai' => $this->masa_hukuman_selesai?->format('Y-m-d'),
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'tamping' => $this->tamping,
            
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'block' => new BlockResource($this->whenLoaded('block')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'documents' => WbpDocumentResource::collection($this->whenLoaded('documents')),
            'family_members' => WbpFamilyMemberResource::collection($this->whenLoaded('familyMembers')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

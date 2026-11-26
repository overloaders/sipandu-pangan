<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplianceRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_komplians' => $this->kode_komplians,
            'judul' => $this->judul,
            'tipe' => $this->tipe,
            'unit_id' => $this->unit_id,
            'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan?->toDateString(),
            'tanggal_berlaku_sampai' => $this->tanggal_berlaku_sampai?->toDateString(),
            'hasil' => $this->hasil,
            'nilai_skor' => $this->nilai_skor,
            'tingkat_risiko' => $this->tingkat_risiko,
            'inspektur_user_id' => $this->inspektur_user_id,
            'status' => $this->status,
            'temuan' => $this->temuan,
            'rekomendasi' => $this->rekomendasi,
            'dokumen_pendukung' => $this->dokumen_pendukung,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'inspektur' => new UserResource($this->whenLoaded('inspektur')),
            'details' => ComplianceDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

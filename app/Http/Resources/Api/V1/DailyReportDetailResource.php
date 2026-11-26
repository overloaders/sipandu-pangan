<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyReportDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori_laporan' => $this->kategori_laporan,
            'judul_detail' => $this->judul_detail,
            'deskripsi' => $this->deskripsi,
            'jumlah' => $this->jumlah,
            'satuan' => $this->satuan,
            'keterangan' => $this->keterangan,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

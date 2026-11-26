<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_laporan' => $this->kode_laporan,
            'tanggal_laporan' => $this->tanggal_laporan?->format('Y-m-d'),
            'status' => $this->status,
            'ringkasan_kegiatan' => $this->ringkasan_kegiatan,
            'catatan_khusus' => $this->catatan_khusus,
            'masalah_ditemukan' => $this->masalah_ditemukan,
            'total_wbp_aktif' => $this->total_wbp_aktif,
            'total_wbp_baru_masuk' => $this->total_wbp_baru_masuk,
            'total_wbp_keluar' => $this->total_wbp_keluar,
            'total_panen_hari_ini_kg' => $this->total_panen_hari_ini_kg,
            'total_pakan_hari_ini_kg' => $this->total_pakan_hari_ini_kg,
            'total_pendapatan_hari_ini' => $this->total_pendapatan_hari_ini,
            'total_pengeluaran_hari_ini' => $this->total_pengeluaran_hari_ini,
            
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'reporter' => new UserResource($this->whenLoaded('reporter')),
            'confirmer' => new UserResource($this->whenLoaded('confirmer')),
            'details' => DailyReportDetailResource::collection($this->whenLoaded('details')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

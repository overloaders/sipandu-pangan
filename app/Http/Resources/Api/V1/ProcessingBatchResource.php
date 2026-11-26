<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcessingBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_batch' => $this->kode_batch,
            'nama_produk' => $this->nama_produk,
            'unit_id' => $this->unit_id,
            'jenis_pengolahan' => $this->jenis_pengolahan,
            'tanggal_produksi' => $this->tanggal_produksi?->toDateString(),
            'tanggal_kadaluarsa' => $this->tanggal_kadaluarsa?->toDateString(),
            'jumlah_produksi' => $this->jumlah_produksi,
            'satuan_output' => $this->satuan_output,
            'biaya_produksi' => $this->biaya_produksi,
            'harga_jual_satuan' => $this->harga_jual_satuan,
            'status' => $this->status,
            'pic_wbp_id' => $this->pic_wbp_id,
            'keterangan' => $this->keterangan,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'pic' => new WbpResource($this->whenLoaded('pic')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

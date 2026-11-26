<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_transaksi_keu' => $this->kode_transaksi_keu,
            'tipe' => $this->tipe,
            'jumlah' => $this->jumlah,
            'deskripsi' => $this->deskripsi,
            'sumber_dana' => $this->sumber_dana,
            'metode_pembayaran' => $this->metode_pembayaran,
            'tanggal_transaksi' => $this->tanggal_transaksi?->format('Y-m-d'),
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            
            'category' => new FinancialCategoryResource($this->whenLoaded('category')),
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_distribusi' => 'required|string|max:30|unique:distributions,kode_distribusi',
            'tipe_distribusi' => 'required|string|max:50',
            'unit_id' => 'required|exists:units,id',
            'tanggal_distribusi' => 'required|date',
            'total_nilai' => 'nullable|numeric|min:0',
            'jumlah_penerima' => 'nullable|integer|min:0',
            'penerima_nama' => 'required|string|max:200',
            'penerima_kontak' => 'nullable|string|max:100',
            'alamat_penerima' => 'nullable|string',
            'status' => 'required|in:direncanakan,dikirim,diterima,dibatalkan',
            'keterangan' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_id' => 'required_with:items|exists:inventory_items,id',
            'items.*.jumlah' => 'required_with:items|numeric|min:0.01',
            'items.*.satuan' => 'nullable|string|max:30',
            'items.*.nilai_satuan' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_distribusi.required' => 'Kode distribusi wajib diisi',
            'kode_distribusi.unique' => 'Kode distribusi sudah digunakan',
            'tipe_distribusi.required' => 'Tipe distribusi wajib dipilih',
            'unit_id.required' => 'Unit wajib dipilih',
            'tanggal_distribusi.required' => 'Tanggal distribusi wajib diisi',
            'penerima_nama.required' => 'Nama penerima wajib diisi',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

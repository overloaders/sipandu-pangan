<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:100',
            'tipe' => 'required|in:pangan,obat,pakan,alat,bahan_baku,produk_jadi,lainnya',
            'satuan_standar' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'tipe.required' => 'Tipe kategori wajib dipilih',
        ];
    }
}

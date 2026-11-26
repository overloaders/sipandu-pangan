<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_kategori' => 'required|string|max:30|unique:inventory_categories,kode_kategori',
            'nama_kategori' => 'required|string|max:100',
            'tipe' => 'required|in:pangan,obat,pakan,alat,bahan_baku,produk_jadi,lainnya',
            'satuan_standar' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kategori.required' => 'Kode kategori wajib diisi',
            'kode_kategori.unique' => 'Kode kategori sudah digunakan',
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'tipe.required' => 'Tipe kategori wajib dipilih',
        ];
    }
}

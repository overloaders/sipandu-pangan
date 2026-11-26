<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancialCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_kategori_keu' => 'required|string|max:30|unique:financial_categories,kode_kategori_keu',
            'nama_kategori' => 'required|string|max:100',
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'parent_type' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:financial_categories,id',
            'keterangan' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kategori_keu.required' => 'Kode kategori keuangan wajib diisi',
            'kode_kategori_keu.unique' => 'Kode kategori keuangan sudah digunakan',
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'tipe.required' => 'Tipe kategori wajib dipilih',
        ];
    }
}

<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinancialCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
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
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'tipe.required' => 'Tipe kategori wajib dipilih',
        ];
    }
}

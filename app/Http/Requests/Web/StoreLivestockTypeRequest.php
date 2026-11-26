<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivestockTypeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_jenis' => 'required|string|max:30|unique:livestock_types,kode_jenis',
            'nama_jenis' => 'required|string|max:100',
            'kategori' => 'required|in:unggas,ruminansia,air,lainnya',
            'berat_rata_kg' => 'nullable|numeric|min:0',
            'harga_beli_per_ekor' => 'nullable|numeric|min:0',
            'harga_jual_per_kg' => 'nullable|numeric|min:0',
            'masa_pemeliharaan_hari' => 'nullable|integer|min:0',
            'pakan_utama' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_jenis.required' => 'Kode jenis wajib diisi',
            'kode_jenis.unique' => 'Kode jenis sudah digunakan',
            'nama_jenis.required' => 'Nama jenis wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
        ];
    }
}

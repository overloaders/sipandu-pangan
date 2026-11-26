<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCropTypeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_tanaman' => 'required|string|max:100',
            'nama_latin' => 'nullable|string|max:150',
            'kategori' => 'required|in:pangan,sayuran,buah,rempah,obat,lainnya',
            'masa_tanam_hari' => 'nullable|integer|min:0',
            'hasil_per_m2_kg' => 'nullable|numeric|min:0',
            'harga_jual_per_kg' => 'nullable|numeric|min:0',
            'musim_tanam' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_tanaman.required' => 'Nama tanaman wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
        ];
    }
}

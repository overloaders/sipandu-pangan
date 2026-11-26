<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_blok' => 'required|string|max:20|unique:blocks,kode_blok',
            'nama_blok' => 'required|string|max:100',
            'jenis_blok' => 'required|in:umum,khusus,isolasi,rawat_inap',
            'kapasitas' => 'required|integer|min:1|max:500',
            'unit_id' => 'required|exists:units,id',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_blok.required' => 'Kode blok wajib diisi',
            'kode_blok.unique' => 'Kode blok sudah ada',
            'nama_blok.required' => 'Nama blok wajib diisi',
            'jenis_blok.required' => 'Jenis blok wajib dipilih',
            'jenis_blok.in' => 'Jenis blok tidak valid',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'kapasitas.max' => 'Kapasitas maksimal 500',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
        ];
    }
}

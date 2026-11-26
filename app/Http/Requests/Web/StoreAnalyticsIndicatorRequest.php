<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalyticsIndicatorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_indikator' => 'required|string|max:30|unique:analytics_indicators,kode_indikator',
            'nama_indikator' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:100',
            'rumus_perhitungan' => 'nullable|string|max:200',
            'satuan' => 'required|string|max:50',
            'target_nilai' => 'required|numeric|min:0',
            'batas_minimum' => 'nullable|numeric',
            'batas_maksimum' => 'nullable|numeric|gte:batas_minimum',
            'unit_id' => 'nullable|exists:units,id',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_indikator.required' => 'Kode indikator wajib diisi',
            'kode_indikator.unique' => 'Kode indikator sudah digunakan',
            'nama_indikator.required' => 'Nama indikator wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
            'satuan.required' => 'Satuan wajib diisi',
            'target_nilai.required' => 'Target nilai wajib diisi',
            'target_nilai.numeric' => 'Target nilai harus berupa angka',
        ];
    }
}

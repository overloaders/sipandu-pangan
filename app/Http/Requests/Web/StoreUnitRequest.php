<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_unit' => 'required|string|max:30|unique:units,kode_unit',
            'nama_unit' => 'required|string|max:200',
            'tipe_unit' => 'required|in:pusat,kanwil,lapas,rutan,lapwan,lpk',
            'parent_id' => 'nullable|exists:units,id',
            'provinsi' => 'nullable|string|max:100',
            'kota_kab' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'kapasitas_wbp' => 'nullable|string|max:10',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_unit.required' => 'Kode unit wajib diisi',
            'kode_unit.unique' => 'Kode unit sudah digunakan',
            'nama_unit.required' => 'Nama unit wajib diisi',
            'tipe_unit.required' => 'Tipe unit wajib dipilih',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

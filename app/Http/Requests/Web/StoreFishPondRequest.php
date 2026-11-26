<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreFishPondRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_kolam' => 'required|string|max:30|unique:fish_ponds,kode_kolam',
            'nama_kolam' => 'required|string|max:150',
            'unit_id' => 'required|exists:units,id',
            'luas_m2' => 'required|numeric|min:0',
            'tipe_kolam' => 'required|in:tanah,semen,terpal,beton,floating_cage',
            'sumber_air' => 'required|in:sungai,sumur,hujan,pompa,PDAM',
            'status' => 'required|in:aktif,kering,renovasi,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kolam.required' => 'Kode kolam wajib diisi',
            'kode_kolam.unique' => 'Kode kolam sudah ada',
            'nama_kolam.required' => 'Nama kolam wajib diisi',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'luas_m2.required' => 'Luas kolam wajib diisi',
            'luas_m2.min' => 'Luas kolam minimal 0',
            'tipe_kolam.required' => 'Tipe kolam wajib dipilih',
            'tipe_kolam.in' => 'Tipe kolam tidak valid',
            'sumber_air.required' => 'Sumber air wajib dipilih',
            'sumber_air.in' => 'Sumber air tidak valid',
        ];
    }
}

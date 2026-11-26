<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivestockBarnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_kandang' => 'required|string|max:30|unique:livestock_barns,kode_kandang',
            'nama_kandang' => 'required|string|max:150',
            'unit_id' => 'required|exists:units,id',
            'jenis_ternak' => 'required|in:ayam,bebek,kambing,sapi,babi,kelinci,kambing_perah,sapi_perah,lainnya',
            'kapasitas' => 'required|integer|min:1',
            'luas_m2' => 'required|numeric|min:0',
            'tipe_kandang' => 'required|in:kandang_baterai,kandang_umbaran,kandang_koloni,semi_ekstensif',
            'status' => 'required|in:aktif,renovasi,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kandang.required' => 'Kode kandang wajib diisi',
            'kode_kandang.unique' => 'Kode kandang sudah ada',
            'nama_kandang.required' => 'Nama kandang wajib diisi',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'jenis_ternak.required' => 'Jenis ternak wajib dipilih',
            'jenis_ternak.in' => 'Jenis ternak tidak valid',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'luas_m2.required' => 'Luas kandang wajib diisi',
            'luas_m2.min' => 'Luas kandang minimal 0',
            'tipe_kandang.required' => 'Tipe kandang wajib dipilih',
            'tipe_kandang.in' => 'Tipe kandang tidak valid',
        ];
    }
}

<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_kamar' => 'required|string|max:20',
            'nama_kamar' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kamar.required' => 'Kode kamar wajib diisi',
            'nama_kamar.required' => 'Nama kamar wajib diisi',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'kapasitas.max' => 'Kapasitas maksimal 50',
        ];
    }
}

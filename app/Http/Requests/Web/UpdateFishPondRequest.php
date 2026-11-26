<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFishPondRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kolam' => 'sometimes|string|max:150',
            'luas_m2' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:aktif,kering,renovasi,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kolam.max' => 'Nama kolam maksimal 150 karakter',
            'luas_m2.min' => 'Luas kolam minimal 0',
            'status.in' => 'Status tidak valid',
        ];
    }
}

<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLivestockBarnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kandang' => 'sometimes|string|max:150',
            'kapasitas' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:aktif,renovasi,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kandang.max' => 'Nama kandang maksimal 150 karakter',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'status.in' => 'Status tidak valid',
        ];
    }
}

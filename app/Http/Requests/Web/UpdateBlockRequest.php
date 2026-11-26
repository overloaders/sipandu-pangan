<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_blok' => 'sometimes|string|max:100',
            'kapasitas' => 'sometimes|integer|min:1|max:500',
            'status' => 'sometimes|in:aktif,renovasi,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_blok.max' => 'Nama blok maksimal 100 karakter',
            'kapasitas.min' => 'Kapasitas minimal 1',
            'kapasitas.max' => 'Kapasitas maksimal 500',
            'status.in' => 'Status tidak valid',
        ];
    }
}

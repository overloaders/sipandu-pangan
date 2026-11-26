<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kapasitas' => 'sometimes|integer|min:1|max:50',
            'status' => 'sometimes|in:tersedia,penuh,renovasi,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kapasitas.min' => 'Kapasitas minimal 1',
            'kapasitas.max' => 'Kapasitas maksimal 50',
            'status.in' => 'Status tidak valid',
        ];
    }
}

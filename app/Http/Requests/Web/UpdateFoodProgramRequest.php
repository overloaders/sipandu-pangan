<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_program' => 'sometimes|string|max:200',
            'status' => 'sometimes|in:perencanaan,aktif,selesai,dibatalkan',
            'deskripsi' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_program.max' => 'Nama program maksimal 200 karakter',
            'status.in' => 'Status tidak valid',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
        ];
    }
}

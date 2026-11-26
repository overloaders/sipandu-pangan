<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:draft,dikirim,dikonfirmasi,dibatalkan',
            'ringkasan_kegiatan' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status tidak valid',
            'ringkasan_kegiatan.max' => 'Ringkasan kegiatan maksimal 2000 karakter',
        ];
    }
}

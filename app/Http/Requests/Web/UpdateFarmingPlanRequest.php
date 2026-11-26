<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:direncanakan,proses_tanam,tumbuh,siap_panen,panen,selesai',
            'tanggal_tanam_aktual' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status tidak valid',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
        ];
    }
}

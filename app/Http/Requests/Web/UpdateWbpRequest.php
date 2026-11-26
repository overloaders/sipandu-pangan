<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWbpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'sometimes|string|max:150',
            'status' => 'sometimes|in:aktif,dikeluarkan,pindah,meninggal,bebas',
            'block_id' => 'nullable|exists:blocks,id',
            'room_id' => 'nullable|exists:rooms,id',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.max' => 'Nama lengkap maksimal 150 karakter',
            'status.in' => 'Status tidak valid',
            'block_id.exists' => 'Blok tidak valid',
            'room_id.exists' => 'Kamar tidak valid',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
        ];
    }
}

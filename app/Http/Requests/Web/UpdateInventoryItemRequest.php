<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_item' => 'sometimes|string|max:200',
            'stok_saat_ini' => 'sometimes|numeric|min:0',
            'stok_minimum' => 'sometimes|numeric|min:0',
            'harga_jual' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:tersedia,stok_habis,kadaluarsa,rusak,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_item.max' => 'Nama item maksimal 200 karakter',
            'stok_saat_ini.min' => 'Stok saat ini minimal 0',
            'stok_minimum.min' => 'Stok minimum minimal 0',
            'harga_jual.min' => 'Harga jual minimal 0',
            'status.in' => 'Status tidak valid',
        ];
    }
}

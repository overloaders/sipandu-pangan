<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockOpnameRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'tanggal_opname' => 'required|date',
            'jenis_opname' => 'required|string|max:50',
            'status' => 'required|in:draft,proses,selesai,dibatalkan',
            'petugas_wbp_id' => 'nullable|exists:wbp,id',
            'verifikator_user_id' => 'nullable|exists:users,id',
            'total_item_checked' => 'nullable|integer|min:0',
            'item_selisih' => 'nullable|integer|min:0',
            'total_selisih_nilai' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'unit_id.required' => 'Unit wajib dipilih',
            'tanggal_opname.required' => 'Tanggal opname wajib diisi',
            'jenis_opname.required' => 'Jenis opname wajib dipilih',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

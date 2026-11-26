<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOpnameRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_opname' => 'required|string|max:30|unique:stock_opnames,kode_opname',
            'unit_id' => 'required|exists:units,id',
            'tanggal_opname' => 'required|date',
            'jenis_opname' => 'required|string|max:50',
            'status' => 'required|in:draft,proses,selesai,dibatalkan',
            'petugas_wbp_id' => 'nullable|exists:wbp,id',
            'verifikator_user_id' => 'nullable|exists:users,id',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_opname.required' => 'Kode opname wajib diisi',
            'kode_opname.unique' => 'Kode opname sudah digunakan',
            'unit_id.required' => 'Unit wajib dipilih',
            'tanggal_opname.required' => 'Tanggal opname wajib diisi',
            'jenis_opname.required' => 'Jenis opname wajib dipilih',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

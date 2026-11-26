<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplianceRecordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:200',
            'tipe' => 'required|string|max:100',
            'unit_id' => 'required|exists:units,id',
            'tanggal_pemeriksaan' => 'required|date',
            'tanggal_berlaku_sampai' => 'nullable|date|after_or_equal:tanggal_pemeriksaan',
            'hasil' => 'nullable|string|max:100',
            'nilai_skor' => 'nullable|numeric|min:0|max:100',
            'tingkat_risiko' => 'nullable|in:rendah,sedang,tinggi,kritis',
            'inspektur_user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,aktif,perlu_perbaikan,kedaluwarsa,dibatalkan',
            'temuan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul wajib diisi',
            'tipe.required' => 'Tipe wajib dipilih',
            'unit_id.required' => 'Unit wajib dipilih',
            'tanggal_pemeriksaan.required' => 'Tanggal pemeriksaan wajib diisi',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

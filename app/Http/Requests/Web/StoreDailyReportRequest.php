<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'tanggal_laporan' => 'required|date|after_or_equal:today',
            'ringkasan_kegiatan' => 'nullable|string|max:2000',
            'catatan_khusus' => 'nullable|string|max:1000',
            'masalah_ditemukan' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'tanggal_laporan.required' => 'Tanggal laporan wajib diisi',
            'tanggal_laporan.after_or_equal' => 'Tanggal laporan harus hari ini atau setelahnya',
            'ringkasan_kegiatan.max' => 'Ringkasan kegiatan maksimal 2000 karakter',
            'catatan_khusus.max' => 'Catatan khusus maksimal 1000 karakter',
            'masalah_ditemukan.max' => 'Masalah ditemukan maksimal 1000 karakter',
        ];
    }
}

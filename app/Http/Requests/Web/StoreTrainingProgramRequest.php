<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_pelatihan' => 'required|string|max:30|unique:training_programs,kode_pelatihan',
            'nama_pelatihan' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:50',
            'unit_id' => 'required|exists:units,id',
            'instructor_user_id' => 'nullable|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_pelatihan' => 'nullable|integer|min:1',
            'kapasitas_peserta' => 'nullable|integer|min:1',
            'biaya' => 'nullable|numeric|min:0',
            'lokasi' => 'nullable|string|max:200',
            'tipe' => 'nullable|string|max:50',
            'status' => 'required|string|in:perencanaan,terbuka,berlangsung,selesai,dibatalkan',
            'sertifikat_diberikan' => 'nullable|boolean',
            'materi_pokok' => 'nullable|string',
        ];
    }
}

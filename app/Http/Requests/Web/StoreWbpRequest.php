<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreWbpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_induk' => 'required|string|max:30|unique:wbp,nomor_induk',
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date|before:today',
            'tempat_lahir' => 'required|string|max:100',
            'nik' => 'required|string|max:20|unique:wbp,nik',
            'agama' => 'required|string|max:20',
            'tanggal_masuk' => 'required|date|after_or_equal:today',
            'masa_hukuman_hari' => 'required|integer|min:1|max:36500',
            'unit_id' => 'required|exists:units,id',
            'block_id' => 'nullable|exists:blocks,id',
            'room_id' => 'nullable|exists:rooms,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_induk.required' => 'Nomor induk wajib diisi',
            'nomor_induk.unique' => 'Nomor induk sudah terdaftar',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.max' => 'Nama lengkap maksimal 150 karakter',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'agama.required' => 'Agama wajib dipilih',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi',
            'tanggal_masuk.after_or_equal' => 'Tanggal masuk harus hari ini atau setelahnya',
            'masa_hukuman_hari.required' => 'Masa hukuman wajib diisi',
            'masa_hukuman_hari.min' => 'Masa hukuman minimal 1 hari',
            'masa_hukuman_hari.max' => 'Masa hukuman maksimal 100 tahun',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'block_id.exists' => 'Blok tidak valid',
            'room_id.exists' => 'Kamar tidak valid',
        ];
    }
}

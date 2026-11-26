<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_program' => 'required|string|max:30|unique:food_programs,kode_program',
            'nama_program' => 'required|string|max:200',
            'jenis_program' => 'required|in:pertanian,peternakan,perikanan,integrated,pengolahan',
            'unit_id' => 'required|exists:units,id',
            'tanggal_mulai' => 'required|date',
            'anggaran' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_program.required' => 'Kode program wajib diisi',
            'kode_program.unique' => 'Kode program sudah ada',
            'nama_program.required' => 'Nama program wajib diisi',
            'jenis_program.required' => 'Jenis program wajib dipilih',
            'jenis_program.in' => 'Jenis program tidak valid',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'anggaran.required' => 'Anggaran wajib diisi',
            'anggaran.min' => 'Anggaran minimal 0',
        ];
    }
}

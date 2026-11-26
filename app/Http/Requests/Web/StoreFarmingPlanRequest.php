<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_rencana' => 'required|string|max:30|unique:farming_plans,kode_rencana',
            'food_program_id' => 'required|exists:food_programs,id',
            'land_plot_id' => 'required|exists:land_plots,id',
            'crop_type_id' => 'required|exists:crop_types,id',
            'jumlah_tanaman' => 'required|integer|min:1',
            'tanggal_tanam_rencana' => 'required|date|after_or_equal:today',
            'luas_tanam_m2' => 'required|numeric|min:0',
            'metode_budi_daya' => 'required|in:konvensional,organik,hidroponik,aeroponik,mixed',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_rencana.required' => 'Kode rencana wajib diisi',
            'kode_rencana.unique' => 'Kode rencana sudah ada',
            'food_program_id.required' => 'Program pangan wajib dipilih',
            'food_program_id.exists' => 'Program pangan tidak valid',
            'land_plot_id.required' => 'Lahan wajib dipilih',
            'land_plot_id.exists' => 'Lahan tidak valid',
            'crop_type_id.required' => 'Jenis tanaman wajib dipilih',
            'crop_type_id.exists' => 'Jenis tanaman tidak valid',
            'jumlah_tanaman.required' => 'Jumlah tanaman wajib diisi',
            'jumlah_tanaman.min' => 'Jumlah tanaman minimal 1',
            'tanggal_tanam_rencana.required' => 'Tanggal tanam rencana wajib diisi',
            'tanggal_tanam_rencana.after_or_equal' => 'Tanggal tanam harus hari ini atau setelahnya',
            'luas_tanam_m2.required' => 'Luas tanam wajib diisi',
            'luas_tanam_m2.min' => 'Luas tanam minimal 0',
            'metode_budi_daya.required' => 'Metode budi daya wajib dipilih',
            'metode_budi_daya.in' => 'Metode budi daya tidak valid',
        ];
    }
}

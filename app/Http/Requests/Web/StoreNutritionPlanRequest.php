<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreNutritionPlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kode_rencana_gizi' => 'required|string|max:30|unique:nutrition_plans,kode_rencana_gizi',
            'nama_menu' => 'required|string|max:200',
            'unit_id' => 'required|exists:units,id',
            'waktu_makan' => 'required|string|max:50',
            'berlaku_mulai' => 'nullable|date',
            'berlaku_selesai' => 'nullable|date|after_or_equal:berlaku_mulai',
            'kalori_target_kcal' => 'nullable|numeric|min:0',
            'protein_target_gr' => 'nullable|numeric|min:0',
            'karbohidrat_target_gr' => 'nullable|numeric|min:0',
            'lemak_target_gr' => 'nullable|numeric|min:0',
            'serat_target_gr' => 'nullable|numeric|min:0',
            'tipe_porsi' => 'required|string|max:50',
            'estimasi_biaya_per_porsi' => 'nullable|numeric|min:0',
            'sumber_bahan' => 'nullable|string|max:200',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:perencanaan,aktif,selesai,dibatalkan',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_rencana_gizi.required' => 'Kode rencana gizi wajib diisi',
            'kode_rencana_gizi.unique' => 'Kode rencana gizi sudah digunakan',
            'nama_menu.required' => 'Nama menu wajib diisi',
            'unit_id.required' => 'Unit wajib dipilih',
            'waktu_makan.required' => 'Waktu makan wajib dipilih',
            'tipe_porsi.required' => 'Tipe porsi wajib dipilih',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

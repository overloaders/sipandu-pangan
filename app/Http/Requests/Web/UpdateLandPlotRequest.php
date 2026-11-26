<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandPlotRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_lahan' => 'required|string|max:200',
            'unit_id' => 'required|exists:units,id',
            'luas_m2' => 'required|numeric|min:0',
            'jenis_tanah' => 'nullable|in:tanah_liat,tanah_berpasir,tanah_laterit,tanah_gambut,tanah_volkanik,campuran',
            'sistem_irigasi' => 'nullable|in:tadah_hujan,irigasi_tetes,parit,pompa_air,sungai,sumur',
            'status' => 'required|in:tersedia,ditanami,istirahat,renovasi,tidak_digunakan',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lahan.required' => 'Nama lahan wajib diisi',
            'unit_id.required' => 'Unit wajib dipilih',
            'luas_m2.required' => 'Luas lahan wajib diisi',
            'luas_m2.numeric' => 'Luas lahan harus berupa angka',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

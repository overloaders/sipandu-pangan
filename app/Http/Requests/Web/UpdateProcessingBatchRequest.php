<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessingBatchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:200',
            'unit_id' => 'required|exists:units,id',
            'jenis_pengolahan' => 'required|string|max:100',
            'tanggal_produksi' => 'required|date',
            'tanggal_kadaluarsa' => 'nullable|date|after:tanggal_produksi',
            'jumlah_produksi' => 'nullable|numeric|min:0',
            'satuan_output' => 'nullable|string|max:30',
            'biaya_produksi' => 'nullable|numeric|min:0',
            'harga_jual_satuan' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,proses,selesai,gagal',
            'pic_wbp_id' => 'nullable|exists:wbp,id',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_produk.required' => 'Nama produk wajib diisi',
            'unit_id.required' => 'Unit wajib dipilih',
            'jenis_pengolahan.required' => 'Jenis pengolahan wajib dipilih',
            'tanggal_produksi.required' => 'Tanggal produksi wajib diisi',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}

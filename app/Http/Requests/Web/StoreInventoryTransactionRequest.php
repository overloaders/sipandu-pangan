<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:inventory_items,id',
            'tipe_transaksi' => 'required|in:masuk,keluar,transfer,opname,rusak,kadaluarsa',
            'jumlah' => 'required|numeric|min:0.01',
            'sumber' => 'required|in:produksi,pembelian,konsumsi_wbp,penjualan,donasi,konversi,opname,lainnya',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Item wajib dipilih',
            'item_id.exists' => 'Item tidak valid',
            'tipe_transaksi.required' => 'Tipe transaksi wajib dipilih',
            'tipe_transaksi.in' => 'Tipe transaksi tidak valid',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.min' => 'Jumlah minimal 0.01',
            'sumber.required' => 'Sumber wajib dipilih',
            'sumber.in' => 'Sumber tidak valid',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
        ];
    }
}

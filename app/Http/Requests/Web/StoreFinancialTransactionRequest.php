<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancialTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'category_id' => 'nullable|exists:financial_categories,id',
            'jumlah' => 'required|numeric|min:0.01',
            'deskripsi' => 'required|string|max:255',
            'sumber_dana' => 'required|in:anggaran,penjualan_pangan,pnbp,hibah,sumbangan,lainnya',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,lainnya',
            'unit_id' => 'required|exists:units,id',
            'tanggal_transaksi' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'tipe.required' => 'Tipe transaksi wajib dipilih',
            'tipe.in' => 'Tipe transaksi tidak valid',
            'category_id.exists' => 'Kategori tidak valid',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.min' => 'Jumlah minimal Rp 1',
            'deskripsi.required' => 'Deskripsi wajib diisi',
            'sumber_dana.required' => 'Sumber dana wajib dipilih',
            'sumber_dana.in' => 'Sumber dana tidak valid',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi',
        ];
    }
}

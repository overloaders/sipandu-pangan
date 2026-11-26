<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_item' => 'required|string|max:30|unique:inventory_items,kode_item',
            'nama_item' => 'required|string|max:200',
            'category_id' => 'nullable|exists:inventory_categories,id',
            'unit_id' => 'required|exists:units,id',
            'satuan' => 'sometimes|string|max:30',
            'stok_saat_ini' => 'sometimes|numeric|min:0',
            'stok_minimum' => 'sometimes|numeric|min:0',
            'harga_beli_rata' => 'sometimes|numeric|min:0',
            'harga_jual' => 'sometimes|numeric|min:0',
            'tipe_pangan' => 'required|in:basah,kering,olahan,bumbu,bahan_baku',
            'asal' => 'required|in:produksi_sendiri,pembelian,hibah,konversi',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_item.required' => 'Kode item wajib diisi',
            'kode_item.unique' => 'Kode item sudah ada',
            'nama_item.required' => 'Nama item wajib diisi',
            'unit_id.required' => 'Unit wajib dipilih',
            'unit_id.exists' => 'Unit tidak valid',
            'category_id.exists' => 'Kategori tidak valid',
            'stok_saat_ini.min' => 'Stok saat ini minimal 0',
            'stok_minimum.min' => 'Stok minimum minimal 0',
            'harga_beli_rata.min' => 'Harga beli minimal 0',
            'harga_jual.min' => 'Harga jual minimal 0',
            'tipe_pangan.required' => 'Tipe pangan wajib dipilih',
            'tipe_pangan.in' => 'Tipe pangan tidak valid',
            'asal.required' => 'Asal item wajib dipilih',
            'asal.in' => 'Asal item tidak valid',
        ];
    }
}

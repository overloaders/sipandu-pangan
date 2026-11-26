<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\InventoryItem;
use App\Models\Unit;
use App\Models\Wbp;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreStockOpnameRequest;
use App\Http\Requests\Web\UpdateStockOpnameRequest;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    use UnitScope;

    public function index(Request $request)
    {
        $query = StockOpname::with(['unit', 'petugasWbp', 'verifikator']);
        $this->applyUnitScope($query, $request);

        if ($request->status) $query->where('status', $request->status);
        if ($request->jenis_opname) $query->where('jenis_opname', $request->jenis_opname);
        if ($request->from_date) $query->where('tanggal_opname', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_opname', '<=', $request->to_date);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_opname', 'like', "%{$request->search}%");
            });
        }

        $opnames = $query->orderByDesc('tanggal_opname')->paginate(15);
        return view('stock-opname.index', compact('opnames'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('stock-opname.create', compact('units', 'wbps'));
    }

    public function store(StoreStockOpnameRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_opname' => 'required|unique:stock_opnames,kode_opname',
            'unit_id' => 'required|exists:units,id',
            'tanggal_opname' => 'required|date',
            'jenis_opname' => 'required',
            'status' => 'required',
        ]);

        StockOpname::create($request->only([
            'kode_opname', 'unit_id', 'tanggal_opname', 'jenis_opname',
            'status', 'petugas_wbp_id', 'verifikator_user_id',
            'keterangan',
        ]));

        return redirect()->route('stock-opname.index')->with('success', 'Stock opname berhasil ditambahkan!');
    }

    public function show(StockOpname $stock_opname)
    {
        $this->authorizeUnitModel($stock_opname);
        $stock_opname->load(['unit', 'petugasWbp', 'verifikator', 'items.item', 'statusHistories.changedBy']);
        // Item inventaris untuk dropdown — scope unit opname (hindari bocor lintas unit)
        $items = InventoryItem::where('unit_id', $stock_opname->unit_id)->orderBy('nama_item')->get();
        return view('stock-opname.show', ['opname' => $stock_opname, 'items' => $items]);
    }

    public function edit(StockOpname $stock_opname)
    {
        $this->authorizeUnitModel($stock_opname);
        if ($stock_opname->status === 'selesai') {
            return redirect()->route('stock-opname.show', $stock_opname->id)
                ->with('error', 'Stock opname yang sudah selesai tidak dapat diedit.');
        }
        $units = $this->allowedUnitsQuery()->get();
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('stock-opname.edit', ['opname' => $stock_opname, 'units' => $units, 'wbps' => $wbps]);
    }

    public function update(UpdateStockOpnameRequest $request, StockOpname $stock_opname)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($stock_opname);
        if ($stock_opname->status === 'selesai') {
            return redirect()->route('stock-opname.show', $stock_opname->id)
                ->with('error', 'Stock opname yang sudah selesai tidak dapat diupdate.');
        }
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'tanggal_opname' => 'required|date',
            'jenis_opname' => 'required',
            'status' => 'required',
        ]);

        $stock_opname->update($request->only([
            'unit_id', 'tanggal_opname', 'jenis_opname',
            'status', 'petugas_wbp_id', 'verifikator_user_id',
            'total_item_checked', 'item_selisih', 'total_selisih_nilai',
            'keterangan',
        ]));

        return redirect()->route('stock-opname.index')->with('success', 'Stock opname berhasil diupdate!');
    }

    public function destroy(StockOpname $stock_opname)
    {
        $this->authorizeUnitModel($stock_opname);
        $stock_opname->delete();
        return redirect()->route('stock-opname.index')->with('success', 'Stock opname berhasil dihapus!');
    }

    // === ITEM OPNAME ===
    private function validateItem(Request $request)
    {
        return $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'stok_sistem' => 'required|numeric|min:0',
            'stok_fisik' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);
    }

    private function withSelisih(array $data, int $itemId): array
    {
        $selisih = (float) $data['stok_fisik'] - (float) $data['stok_sistem'];
        $data['selisih'] = $selisih;
        $harga = InventoryItem::find($itemId)?->harga_beli_rata ?? 0;
        $data['nilai_selisih'] = $selisih * (float) $harga;
        return $data;
    }

    public function storeItem(Request $request, StockOpname $stock_opname)
    {
        $this->authorizeUnitModel($stock_opname);
        $data = $this->withSelisih($this->validateItem($request), (int) $request->item_id);
        // Satu item hanya boleh satu baris per opname
        if (StockOpnameItem::where('opname_id', $stock_opname->id)->where('item_id', $data['item_id'])->exists()) {
            return back()->with('error', 'Item ini sudah ada dalam opname — gunakan Edit.');
        }
        $data['opname_id'] = $stock_opname->id;
        StockOpnameItem::create($data);
        return back()->with('success', 'Item opname berhasil ditambahkan');
    }

    public function updateItem(Request $request, StockOpname $stock_opname, StockOpnameItem $item)
    {
        $this->authorizeUnitModel($stock_opname);
        $data = $this->withSelisih($this->validateItem($request), (int) $request->item_id);
        // Cegah duplikat (unique opname_id+item_id) saat item diganti ke item yang sudah ada
        if (StockOpnameItem::where('opname_id', $stock_opname->id)
            ->where('item_id', $data['item_id'])
            ->where('id', '!=', $item->id)
            ->exists()) {
            return back()->with('error', 'Item ini sudah ada dalam opname.');
        }
        $item->update($data);
        return back()->with('success', 'Item opname berhasil diperbarui');
    }

    public function destroyItem(StockOpname $stock_opname, StockOpnameItem $item)
    {
        $this->authorizeUnitModel($stock_opname);
        $item->delete();
        return back()->with('success', 'Item opname berhasil dihapus');
    }

    /**
     * Ubah status stock opname (aksi cepat dari halaman detail).
     * Status selesai bersifat final.
     */
    public function updateStatus(Request $request, StockOpname $stock_opname)
    {
        $this->authorizeUnitModel($stock_opname);
        $request->validate(['status' => 'required|in:draft,proses,selesai,dibatalkan']);
        if ($stock_opname->status === 'selesai') {
            return back()->with('error', 'Stock opname yang sudah selesai tidak dapat diubah statusnya.');
        }
        $stock_opname->update(['status' => $request->status]);
        return back()->with('success', 'Status stock opname diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

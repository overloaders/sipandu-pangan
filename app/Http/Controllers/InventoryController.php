<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\Unit;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreInventoryItemRequest;
use App\Http\Requests\Web\UpdateInventoryItemRequest;

class InventoryController extends Controller
{
    use UnitScope;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->canInput()) {
                abort(403, 'Anda tidak memiliki akses ke modul ini.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy', 'updateStatus']);
    }

    public function index(Request $request)
    {
        $query = InventoryItem::with(['category', 'unit'])->withCount('statusHistories');
        $this->applyUnitScope($query, $request);
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_item', 'LIKE', "%{$search}%")->orWhere('kode_item', 'LIKE', "%{$search}%");
            });
        }
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->status) $query->where('status', $request->status);
        $items = $query->orderBy('nama_item')->paginate(15)->withQueryString();

        // Statistik stok (ber-scope unit) — dihitung di controller agar tidak query di view & tidak bocor lintas unit
        $stokTersedia = InventoryItem::where('status', 'tersedia')->whereColumn('stok_saat_ini', '>', 'stok_minimum');
        $this->applyUnitScope($stokTersedia, $request);
        $stokMenipis = InventoryItem::where('status', 'tersedia')->whereColumn('stok_saat_ini', '<=', 'stok_minimum')->where('stok_saat_ini', '>', 0);
        $this->applyUnitScope($stokMenipis, $request);
        $stokHabis = InventoryItem::where('status', 'tersedia')->where('stok_saat_ini', '=', 0);
        $this->applyUnitScope($stokHabis, $request);

        $stokRusak = InventoryItem::where('status', 'rusak');
        $this->applyUnitScope($stokRusak, $request);
        $totalItem = InventoryItem::query();
        $this->applyUnitScope($totalItem, $request);

        $stats = [
            'tersedia' => $stokTersedia->count(),
            'menipis' => $stokMenipis->count(),
            'habis' => $stokHabis->count(),
            'rusak' => $stokRusak->count(),
            'total' => $totalItem->count(),
        ];
        $categories = InventoryCategory::orderBy('nama_kategori')->get();
        return view('inventory.index', compact('items', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = InventoryCategory::orderBy('nama_kategori')->get();
        $units = $this->allowedUnitsQuery()->get();
        return view('inventory.create', compact('categories', 'units'));
    }

    public function store(StoreInventoryItemRequest $request)
    {
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        $data['status'] = 'tersedia';
        InventoryItem::create($data);
        return redirect()->route('inventory.index')->with('success', 'Item berhasil ditambahkan');
    }

    public function show(InventoryItem $inventory)
    {
        $this->authorizeUnitModel($inventory);
        $inventory->load(['category', 'unit', 'statusHistories.changedBy', 'transactions' => fn($q) => $q->latest()->limit(20)]);
        return view('inventory.show', ['item' => $inventory]);
    }

    public function edit(InventoryItem $inventory)
    {
        $this->authorizeUnitModel($inventory);
        $categories = InventoryCategory::orderBy('nama_kategori')->get();
        $units = $this->allowedUnitsQuery()->get();
        return view('inventory.edit', ['item' => $inventory, 'categories' => $categories, 'units' => $units]);
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventory)
    {
        $this->authorizeUnitModel($inventory);
        $inventory->update($this->enforceUnitId($request->validated()));
        return redirect()->route('inventory.index')->with('success', 'Item berhasil diperbarui');
    }

    public function destroy(InventoryItem $inventory)
    {
        $this->authorizeUnitModel($inventory);
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Item berhasil dihapus');
    }

    /**
     * Ubah status item inventori (aksi cepat dari halaman detail).
     */
    public function updateStatus(Request $request, InventoryItem $inventory)
    {
        $this->authorizeUnitModel($inventory);
        $request->validate([
            'status' => 'required|in:tersedia,stok_habis,kadaluarsa,rusak,nonaktif',
        ]);

        $inventory->update(['status' => $request->status]);
        return back()->with('success', 'Status item diperbarui menjadi ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }
}

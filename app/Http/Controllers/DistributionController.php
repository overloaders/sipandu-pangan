<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\DistributionItem;
use App\Models\InventoryItem;
use App\Models\FoodProgram;
use App\Models\Unit;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreDistributionRequest;
use App\Http\Requests\Web\UpdateDistributionRequest;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    use UnitScope;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->canInput()) {
                abort(403, 'Anda tidak memiliki akses ke modul ini.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Distribution::with(['foodProgram', 'items.item', 'creator']);
        $this->applyUnitScope($query, $request);

        if ($request->status) $query->where('status', $request->status);
        if ($request->tipe_distribusi) $query->where('tipe_distribusi', $request->tipe_distribusi);
        if ($request->filled('from_date')) $query->where('tanggal_distribusi', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->where('tanggal_distribusi', '<=', $request->to_date);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_distribusi', 'like', "%{$request->search}%")
                  ->orWhere('penerima_nama', 'like', "%{$request->search}%");
            });
        }

        $distributions = $query->orderByDesc('tanggal_distribusi')->paginate(15);
        return view('distributions.index', compact('distributions'));
    }

    public function create()
    {
        $foodPrograms = FoodProgram::where('status', 'aktif')->orderBy('nama_program')->get();
        $units = $this->allowedUnitsQuery()->get();
        $items = InventoryItem::orderBy('nama_item')->get();
        return view('distributions.create', compact('foodPrograms', 'units', 'items'));
    }

    public function store(StoreDistributionRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_distribusi' => 'required|unique:distributions,kode_distribusi',
            'tipe_distribusi' => 'required',
            'unit_id' => 'required|exists:units,id',
            'tanggal_distribusi' => 'required|date',
            'penerima_nama' => 'required',
            'status' => 'required',
        ]);

        $distribution = Distribution::create($request->only([
            'kode_distribusi', 'tipe_distribusi', 'unit_id', 'tanggal_distribusi',
            'total_nilai', 'jumlah_penerima', 'penerima_nama', 'penerima_kontak',
            'alamat_penerima', 'status', 'created_by_user_id', 'keterangan',
        ]));

        // Create distribution items if provided
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (!empty($item['item_id'])) {
                    $distribution->items()->create([
                        'item_id' => $item['item_id'],
                        'jumlah' => $item['jumlah'] ?? 0,
                        'harga_satuan' => $item['harga_satuan'] ?? 0,
                        'subtotal' => ($item['jumlah'] ?? 0) * ($item['harga_satuan'] ?? 0),
                        'satuan' => $item['satuan'] ?? 'kg',
                    ]);
                }
            }
        }

        return redirect()->route('distributions.index')->with('success', 'Distribusi berhasil ditambahkan!');
    }

    public function show(Distribution $distribution)
    {
        $this->authorizeUnitModel($distribution);
        $distribution->load(['foodProgram', 'items.item', 'creator', 'statusHistories.changedBy']);
        // Item inventaris untuk dropdown — scope unit distribusi (hindari bocor lintas unit)
        $items = InventoryItem::where('unit_id', $distribution->unit_id)->orderBy('nama_item')->get();
        return view('distributions.show', compact('distribution', 'items'));
    }

    public function edit(Distribution $distribution)
    {
        $this->authorizeUnitModel($distribution);
        if (in_array($distribution->status, ['diterima', 'dibatalkan'])) {
            return redirect()->route('distributions.show', $distribution->id)
                ->with('error', 'Distribusi dengan status "' . ucfirst($distribution->status) . '" tidak dapat diedit.');
        }
        $distribution->load(['items.item']);
        $foodPrograms = FoodProgram::where('status', 'aktif')->orderBy('nama_program')->get();
        $units = $this->allowedUnitsQuery()->get();
        $items = InventoryItem::orderBy('nama_item')->get();
        return view('distributions.edit', compact('distribution', 'foodPrograms', 'units', 'items'));
    }

    public function update(UpdateDistributionRequest $request, Distribution $distribution)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($distribution);
        if (in_array($distribution->status, ['diterima', 'dibatalkan'])) {
            return redirect()->route('distributions.show', $distribution->id)
                ->with('error', 'Distribusi dengan status "' . ucfirst($distribution->status) . '" tidak dapat diupdate.');
        }
        $request->validate([
            'tipe_distribusi' => 'required',
            'unit_id' => 'required|exists:units,id',
            'tanggal_distribusi' => 'required|date',
            'penerima_nama' => 'required',
            'status' => 'required',
        ]);

        $distribution->update($request->only([
            'tipe_distribusi', 'unit_id', 'tanggal_distribusi',
            'total_nilai', 'jumlah_penerima', 'penerima_nama', 'penerima_kontak',
            'alamat_penerima', 'status', 'keterangan',
        ]));

        return redirect()->route('distributions.index')->with('success', 'Distribusi berhasil diupdate!');
    }

    public function destroy(Distribution $distribution)
    {
        $this->authorizeUnitModel($distribution);
        $distribution->delete();
        return redirect()->route('distributions.index')->with('success', 'Distribusi berhasil dihapus!');
    }

    // === ITEM DISTRIBUSI ===
    private function validateItem(Request $request)
    {
        return $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'jumlah' => 'required|numeric|min:0',
            'harga_satuan' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string|max:1000',
        ]);
    }

    private function withSubtotal(array $data): array
    {
        $data['subtotal'] = (float) $data['jumlah'] * (float) ($data['harga_satuan'] ?? 0);
        $data['satuan'] = $data['satuan'] ?? 'kg';
        return $data;
    }

    public function storeItem(Request $request, Distribution $distribution)
    {
        $this->authorizeUnitModel($distribution);
        $data = $this->withSubtotal($this->validateItem($request));
        $data['distribution_id'] = $distribution->id;
        DistributionItem::create($data);
        return back()->with('success', 'Item distribusi berhasil ditambahkan');
    }

    public function updateItem(Request $request, Distribution $distribution, DistributionItem $item)
    {
        $this->authorizeUnitModel($distribution);
        $item->update($this->withSubtotal($this->validateItem($request)));
        return back()->with('success', 'Item distribusi berhasil diperbarui');
    }

    public function destroyItem(Distribution $distribution, DistributionItem $item)
    {
        $this->authorizeUnitModel($distribution);
        $item->delete();
        return back()->with('success', 'Item distribusi berhasil dihapus');
    }

    /**
     * Ubah status distribusi (aksi cepat dari halaman detail).
     * Status diterima / dibatalkan bersifat final.
     */
    public function updateStatus(Request $request, Distribution $distribution)
    {
        $this->authorizeUnitModel($distribution);
        $request->validate(['status' => 'required|in:draft,dikirim,diterima,dibatalkan']);
        if (in_array($distribution->status, ['diterima', 'dibatalkan'])) {
            return back()->with('error', 'Distribusi dengan status "' . ucfirst($distribution->status) . '" tidak dapat diubah.');
        }
        $distribution->update(['status' => $request->status]);
        return back()->with('success', 'Status distribusi diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

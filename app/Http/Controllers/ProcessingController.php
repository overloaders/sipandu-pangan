<?php

namespace App\Http\Controllers;

use App\Models\ProcessingBatch;
use App\Models\ProcessingBatchItem;
use App\Models\InventoryItem;
use App\Models\FoodProgram;
use App\Models\Unit;
use App\Models\Wbp;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreProcessingBatchRequest;
use App\Http\Requests\Web\UpdateProcessingBatchRequest;
use Illuminate\Http\Request;

class ProcessingController extends Controller
{
    use UnitScope;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->hasRole(['super-admin', 'admin', 'supervisor'])) {
                abort(403, 'Anda tidak memiliki akses ke modul ini.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = ProcessingBatch::with(['unit', 'picWbp']);
        $this->applyUnitScope($query, $request);

        if ($request->status) $query->where('status', $request->status);
        if ($request->jenis_pengolahan) $query->where('jenis_pengolahan', $request->jenis_pengolahan);
        if ($request->from_date) $query->where('tanggal_produksi', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_produksi', '<=', $request->to_date);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_batch', 'like', "%{$request->search}%")
                  ->orWhere('nama_produk', 'like', "%{$request->search}%");
            });
        }

        $batches = $query->orderByDesc('tanggal_produksi')->paginate(15);
        return view('processing.index', compact('batches'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('processing.create', compact('units', 'wbps'));
    }

    public function store(StoreProcessingBatchRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_batch' => 'required|unique:processing_batches,kode_batch',
            'nama_produk' => 'required',
            'unit_id' => 'required|exists:units,id',
            'jenis_pengolahan' => 'required',
            'tanggal_produksi' => 'required|date',
            'status' => 'required',
        ]);

        ProcessingBatch::create($request->only([
            'kode_batch', 'nama_produk', 'unit_id', 'jenis_pengolahan',
            'tanggal_produksi', 'tanggal_kadaluarsa', 'jumlah_produksi',
            'satuan_output', 'biaya_produksi', 'harga_jual_satuan',
            'status', 'pic_wbp_id', 'keterangan',
        ]));

        return redirect()->route('processing.index')->with('success', 'Batch pengolahan berhasil ditambahkan!');
    }

    public function show(ProcessingBatch $processing)
    {
        $this->authorizeUnitModel($processing);
        $processing->load(['unit', 'picWbp', 'items.item', 'statusHistories.changedBy']);
        // Bahan baku untuk dropdown — scope unit batch (hindari bocor lintas unit)
        $items = InventoryItem::where('unit_id', $processing->unit_id)->orderBy('nama_item')->get();
        return view('processing.show', ['batch' => $processing, 'items' => $items]);
    }

    public function edit(ProcessingBatch $processing)
    {
        $this->authorizeUnitModel($processing);
        if (in_array($processing->status, ['selesai', 'gagal'])) {
            return redirect()->route('processing.show', $processing->id)
                ->with('error', 'Batch dengan status "' . ucfirst($processing->status) . '" tidak dapat diedit.');
        }
        $units = $this->allowedUnitsQuery()->get();
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('processing.edit', ['batch' => $processing, 'units' => $units, 'wbps' => $wbps]);
    }

    public function update(UpdateProcessingBatchRequest $request, ProcessingBatch $processing)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($processing);
        if (in_array($processing->status, ['selesai', 'gagal'])) {
            return redirect()->route('processing.show', $processing->id)
                ->with('error', 'Batch dengan status "' . ucfirst($processing->status) . '" tidak dapat diupdate.');
        }
        $request->validate([
            'nama_produk' => 'required',
            'unit_id' => 'required|exists:units,id',
            'jenis_pengolahan' => 'required',
            'tanggal_produksi' => 'required|date',
            'status' => 'required',
        ]);

        $processing->update($request->only([
            'nama_produk', 'unit_id', 'jenis_pengolahan',
            'tanggal_produksi', 'tanggal_kadaluarsa', 'jumlah_produksi',
            'satuan_output', 'biaya_produksi', 'harga_jual_satuan',
            'status', 'pic_wbp_id', 'keterangan',
        ]));

        return redirect()->route('processing.index')->with('success', 'Batch pengolahan berhasil diupdate!');
    }

    public function destroy(ProcessingBatch $processing)
    {
        $this->authorizeUnitModel($processing);
        $processing->delete();
        return redirect()->route('processing.index')->with('success', 'Batch pengolahan berhasil dihapus!');
    }

    // === BAHAN BAKU (ITEM) ===
    public function storeItem(Request $request, ProcessingBatch $processing)
    {
        $this->authorizeUnitModel($processing);
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'jumlah_digunakan' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:30',
            'nilai_bahan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);
        $data['batch_id'] = $processing->id;
        $data['satuan'] = $data['satuan'] ?? 'kg';
        $data['nilai_bahan'] = $data['nilai_bahan'] ?? 0;
        ProcessingBatchItem::create($data);
        return back()->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function updateItem(Request $request, ProcessingBatch $processing, ProcessingBatchItem $item)
    {
        $this->authorizeUnitModel($processing);
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'jumlah_digunakan' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:30',
            'nilai_bahan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);
        $item->update($data);
        return back()->with('success', 'Bahan baku berhasil diperbarui');
    }

    public function destroyItem(ProcessingBatch $processing, ProcessingBatchItem $item)
    {
        $this->authorizeUnitModel($processing);
        $item->delete();
        return back()->with('success', 'Bahan baku berhasil dihapus');
    }

    /**
     * Ubah status batch pengolahan (aksi cepat dari halaman detail).
     * Status selesai / gagal bersifat final.
     */
    public function updateStatus(Request $request, ProcessingBatch $processing)
    {
        $this->authorizeUnitModel($processing);
        $request->validate(['status' => 'required|in:draft,proses,selesai,gagal']);
        if (in_array($processing->status, ['selesai', 'gagal'])) {
            return back()->with('error', 'Batch dengan status "' . ucfirst($processing->status) . '" tidak dapat diubah.');
        }
        $processing->update(['status' => $request->status]);
        return back()->with('success', 'Status batch diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

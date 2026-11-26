<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Room;
use App\Models\Unit;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreBlockRequest;
use App\Http\Requests\Web\UpdateBlockRequest;

class BlockController extends Controller
{
    use UnitScope;
    public function index(Request $request)
    {
        $query = Block::with(['rooms' => fn($q) => $q->select('id', 'block_id', 'kode_kamar', 'nama_kamar', 'kapasitas', 'terisi', 'status')]);
        $this->applyUnitScope($query, $request);
        if ($request->unit_id) $query->where('unit_id', $request->unit_id);
        $blocks = $query->orderBy('kode_blok')->paginate(15)->withQueryString();
        $units = $this->allowedUnitsQuery()->get();

        // Total kamar ber-scope unit (via relasi blok) — rooms tidak punya kolom unit_id
        $roomQuery = Room::query();
        $roomUnitIds = $this->allowedUnitIds();
        if ($request->filled('unit_id')) {
            $roomQuery->whereHas('block', fn ($q) => $q->where('unit_id', $request->unit_id));
        } elseif ($roomUnitIds !== null) {
            $roomQuery->whereHas('block', fn ($q) => $q->whereIn('unit_id', $roomUnitIds));
        }
        $totalRooms = $roomQuery->count();

        return view('blocks.index', compact('blocks', 'units', 'totalRooms'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        return view('blocks.create', compact('units'));
    }

    public function store(StoreBlockRequest $request)
    {
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        $data['status'] = 'aktif';
        Block::create($data);
        return redirect()->route('blocks.index')->with('success', 'Blok berhasil dibuat');
    }

    public function show(Block $block)
    {
        $this->authorizeUnitModel($block);
        $block->load(['rooms.statusHistories.changedBy', 'statusHistories.changedBy', 'wbps' => fn($q) => $q->select('id', 'nomor_induk', 'nama_lengkap', 'block_id', 'room_id', 'status')]);
        return view('blocks.show', compact('block'));
    }

    public function edit(Block $block)
    {
        $this->authorizeUnitModel($block);
        $units = $this->allowedUnitsQuery()->get();
        return view('blocks.edit', compact('block', 'units'));
    }

    public function update(UpdateBlockRequest $request, Block $block)
    {
        $this->authorizeUnitModel($block);
        $block->update($this->enforceUnitId($request->validated()));
        return redirect()->route('blocks.index')->with('success', 'Blok berhasil diperbarui');
    }

    public function destroy(Block $block)
    {
        $this->authorizeUnitModel($block);
        $block->delete();
        return redirect()->route('blocks.index')->with('success', 'Blok berhasil dihapus');
    }

    /**
     * Ubah status blok (aksi cepat dari halaman detail).
     */
    public function updateStatus(Request $request, Block $block)
    {
        $this->authorizeUnitModel($block);
        $request->validate(['status' => 'required|in:aktif,renovasi,nonaktif']);
        $block->update(['status' => $request->status]);
        return back()->with('success', 'Status blok diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

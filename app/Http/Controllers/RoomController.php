<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreRoomRequest;
use App\Http\Requests\Web\UpdateRoomRequest;
use App\Traits\UnitScope;

class RoomController extends Controller
{
    use UnitScope;
    public function index(Block $block)
    {
        return redirect()->route('blocks.show', $block);
    }

    public function show(Block $block, Room $room)
    {
        return redirect()->route('blocks.show', $block);
    }

    public function store(StoreRoomRequest $request, Block $block)
    {
        $this->authorizeUnitModel($block);
        $data = $request->validated();
        $data['block_id'] = $block->id;
        $data['status'] = 'tersedia';
        Room::create($data);
        return redirect()->route('blocks.show', $block)->with('success', 'Kamar berhasil dibuat');
    }

    public function edit(Block $block, Room $room)
    {
        return view('blocks.rooms.edit', ['block' => $block, 'room' => $room]);
    }

    public function update(UpdateRoomRequest $request, Block $block, Room $room)
    {
        $this->authorizeUnitModel($block);
        $room->update($request->validated());
        return redirect()->route('blocks.show', $block)->with('success', 'Kamar berhasil diperbarui');
    }

    public function destroy(Block $block, Room $room)
    {
        $this->authorizeUnitModel($block);
        $room->delete();
        return redirect()->route('blocks.show', $block)->with('success', 'Kamar berhasil dihapus');
    }

    /**
     * Ubah status kamar (aksi cepat dari halaman detail blok).
     */
    public function updateStatus(Request $request, Block $block, Room $room)
    {
        $this->authorizeUnitModel($block);
        $request->validate(['status' => 'required|in:tersedia,penuh,renovasi,nonaktif']);
        $room->update(['status' => $request->status]);
        return back()->with('success', 'Status kamar ' . $room->kode_kamar . ' diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

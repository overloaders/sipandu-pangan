<?php

namespace App\Http\Controllers;

use App\Models\FishPond;
use App\Models\FishPondParticipant;
use Illuminate\Http\Request;
use App\Traits\UnitScope;

class FishPondParticipantController extends Controller
{
    use UnitScope;
    public function store(Request $request, FishPond $fishPond)
    {
        $this->authorizeUnitModel($fishPond);
        $request->validate([
            'wbp_id' => 'required|exists:wbp,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,selesai,dikeluarkan',
            'peran' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $exists = FishPondParticipant::where('fish_pond_id', $fishPond->id)
            ->where('wbp_id', $request->wbp_id)
            ->where('status', '!=', 'dikeluarkan')
            ->exists();

        if ($exists) {
            return back()->with('error', 'WBP ini sudah terdaftar di kolam ini!');
        }

        FishPondParticipant::create([
            'fish_pond_id' => $fishPond->id,
            'wbp_id' => $request->wbp_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
            'peran' => $request->peran,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function destroy(FishPond $fishPond, FishPondParticipant $participant)
    {
        if ($participant->fish_pond_id !== $fishPond->id) {
            abort(404);
        }

        $participant->delete();

        return back()->with('success', 'Peserta berhasil dihapus!');
    }
}

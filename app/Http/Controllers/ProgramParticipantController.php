<?php

namespace App\Http\Controllers;

use App\Models\FoodProgram;
use App\Models\ProgramParticipant;
use App\Models\Wbp;
use Illuminate\Http\Request;
use App\Traits\UnitScope;

class ProgramParticipantController extends Controller
{
    use UnitScope;
    public function store(Request $request, FoodProgram $foodProgram)
    {
        $this->authorizeUnitModel($foodProgram);
        $request->validate([
            'wbp_id' => 'required|exists:wbp,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,selesai,dikeluarkan',
            'peran' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Cek apakah WBP sudah terdaftar di program ini
        $exists = ProgramParticipant::where('food_program_id', $foodProgram->id)
            ->where('wbp_id', $request->wbp_id)
            ->where('status', '!=', 'dikeluarkan')
            ->exists();

        if ($exists) {
            return back()->with('error', 'WBP ini sudah terdaftar di program ini!');
        }

        ProgramParticipant::create([
            'food_program_id' => $foodProgram->id,
            'wbp_id' => $request->wbp_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
            'peran' => $request->peran,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function destroy(FoodProgram $foodProgram, ProgramParticipant $participant)
    {
        if ($participant->food_program_id !== $foodProgram->id) {
            abort(404);
        }

        $participant->delete();

        return back()->with('success', 'Peserta berhasil dihapus!');
    }
}

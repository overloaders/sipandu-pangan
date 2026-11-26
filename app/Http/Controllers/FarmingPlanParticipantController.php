<?php

namespace App\Http\Controllers;

use App\Models\FarmingPlan;
use App\Models\FarmingPlanParticipant;
use Illuminate\Http\Request;
use App\Traits\UnitScope;

class FarmingPlanParticipantController extends Controller
{
    use UnitScope;
    public function store(Request $request, FarmingPlan $farmingPlan)
    {
        $this->authorizeUnitModel($farmingPlan);
        $request->validate([
            'wbp_id' => 'required|exists:wbp,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,selesai,dikeluarkan',
            'peran' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Cek apakah WBP sudah terdaftar di rencana kerja ini
        $exists = FarmingPlanParticipant::where('farming_plan_id', $farmingPlan->id)
            ->where('wbp_id', $request->wbp_id)
            ->where('status', '!=', 'dikeluarkan')
            ->exists();

        if ($exists) {
            return back()->with('error', 'WBP ini sudah terdaftar di rencana kerja ini!');
        }

        FarmingPlanParticipant::create([
            'farming_plan_id' => $farmingPlan->id,
            'wbp_id' => $request->wbp_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
            'peran' => $request->peran,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function destroy(FarmingPlan $farmingPlan, FarmingPlanParticipant $participant)
    {
        if ($participant->farming_plan_id !== $farmingPlan->id) {
            abort(404);
        }

        $participant->delete();

        return back()->with('success', 'Peserta berhasil dihapus!');
    }
}

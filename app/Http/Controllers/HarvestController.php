<?php

namespace App\Http\Controllers;

use App\Models\Harvest;
use App\Models\FarmingPlan;
use App\Traits\UnitScope;
use Illuminate\Http\Request;

class HarvestController extends Controller {
    use UnitScope;
    public function index(Request $request)
    {
        $query = Harvest::with(['farmingPlan.cropType', 'farmingPlan.landPlot']);
        $this->applyUnitScope($query, $request);

        // Search by kode_panen
        if ($search = $request->input('search')) {
            $query->where('kode_panen', 'LIKE', "%{$search}%");
        }

        // Filter by kualitas
        if ($request->filled('kualitas')) {
            $query->where('kualitas', $request->kualitas);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('tanggal_panen', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('tanggal_panen', '<=', $request->to_date);
        }

        // Filter by farming plan
        if ($request->filled('farming_plan_id')) {
            $query->where('farming_plan_id', $request->farming_plan_id);
        }

        $harvests = $query->orderByDesc('tanggal_panen')->paginate(15)->withQueryString();

        // Summary stats
        $totalPanen = Harvest::sum('total_berat_kg');
        $totalPendapatan = Harvest::sum('total_pendapatan');
        $panenBulanIni = Harvest::whereMonth('tanggal_panen', now()->month)
            ->whereYear('tanggal_panen', now()->year)->sum('total_berat_kg');
        $pendapatanBulanIni = Harvest::whereMonth('tanggal_panen', now()->month)
            ->whereYear('tanggal_panen', now()->year)->sum('total_pendapatan');

        return view('harvests.index', compact('harvests', 'totalPanen', 'totalPendapatan', 'panenBulanIni', 'pendapatanBulanIni'));
    }

    public function create()
    {
        $farmingPlans = FarmingPlan::with(['cropType', 'landPlot'])
            ->whereIn('status', ['siap_panen', 'panen', 'selesai'])
            ->orderBy('kode_rencana')
            ->get();

        return view('harvests.create', compact('farmingPlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_panen' => 'required|string|max:30|unique:harvests,kode_panen',
            'farming_plan_id' => 'required|exists:farming_plans,id',
            'tanggal_panen' => 'required|date',
            'total_berat_kg' => 'required|numeric|min:0',
            'total_pendapatan' => 'nullable|numeric|min:0',
            'kualitas' => 'required|in:A,B,C,D',
            'harga_jual_per_kg' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['unit_id'] = auth()->user()->unit_id;
        Harvest::create($validated);
        return redirect()->route('harvests.index')->with('success', 'Data panen berhasil ditambahkan');
    }

    public function show(Harvest $harvest)
    {
        $this->authorizeUnitModel($harvest);
        $harvest->load(['farmingPlan.cropType', 'farmingPlan.landPlot', 'farmingPlan.foodProgram', 'details']);
        return view('harvests.show', compact('harvest'));
    }

    public function edit(Harvest $harvest)
    {
        $this->authorizeUnitModel($harvest);
        $farmingPlans = FarmingPlan::with(['cropType', 'landPlot'])->orderBy('kode_rencana')->get();
        return view('harvests.edit', compact('harvest', 'farmingPlans'));
    }

    public function update(Request $request, Harvest $harvest)
    {
        $this->authorizeUnitModel($harvest);
        $validated = $request->validate([
            'farming_plan_id' => 'required|exists:farming_plans,id',
            'tanggal_panen' => 'required|date',
            'total_berat_kg' => 'required|numeric|min:0',
            'total_pendapatan' => 'nullable|numeric|min:0',
            'kualitas' => 'required|in:A,B,C,D',
            'harga_jual_per_kg' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $harvest->update($validated);
        return redirect()->route('harvests.index')->with('success', 'Data panen berhasil diperbarui');
    }

    public function destroy(Harvest $harvest)
    {
        $this->authorizeUnitModel($harvest);
        $harvest->delete();
        return redirect()->route('harvests.index')->with('success', 'Data panen berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FarmingPlan;
use App\Models\FarmingActivity;
use App\Models\Harvest;
use App\Models\FoodProgram;
use App\Models\LandPlot;
use App\Models\CropType;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreFarmingPlanRequest;
use App\Http\Requests\Web\UpdateFarmingPlanRequest;

class FarmingPlanController extends Controller
{
    use UnitScope;
    public function index(Request $request)
    {
        $query = FarmingPlan::with(['landPlot', 'cropType', 'foodProgram']);
        $this->applyUnitScope($query, $request);
        if ($request->status) $query->where('status', $request->status);
        if ($request->from_date) $query->where('tanggal_tanam_rencana', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_tanam_rencana', '<=', $request->to_date);
        if ($search = $request->input('search')) $query->where('kode_rencana', 'LIKE', "%{$search}%");
        $plans = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Statistik (ber-scope unit) — dihitung di controller agar tidak query di view & tidak bocor lintas unit
        $lahanTersedia = LandPlot::where('status', 'tersedia');
        $this->applyUnitScope($lahanTersedia, $request);
        $rencanaAktif = FarmingPlan::whereIn('status', ['proses_tanam', 'tumbuh']);
        $this->applyUnitScope($rencanaAktif, $request);
        $panenBulanIni = Harvest::where('tanggal_panen', '>=', now()->startOfMonth());
        $this->applyUnitScope($panenBulanIni, $request);

        $stats = [
            'lahan_tersedia' => $lahanTersedia->count(),
            'rencana_aktif' => $rencanaAktif->count(),
            'panen_berat' => (float) $panenBulanIni->sum('total_berat_kg'),
            'panen_pendapatan' => (float) (clone $panenBulanIni)->sum('total_pendapatan'),
        ];
        return view('farming.index', compact('plans', 'stats'));
    }

    public function create()
    {
        $programs = FoodProgram::where('jenis_program', 'pertanian')->whereIn('status', ['aktif', 'perencanaan'])->orderBy('nama_program')->get();
        $landPlots = LandPlot::where('status', 'tersedia')->orderBy('nama_lahan')->get();
        $cropTypes = CropType::orderBy('nama_tanaman')->get();
        return view('farming.create', compact('programs', 'landPlots', 'cropTypes'));
    }

    public function store(StoreFarmingPlanRequest $request)
    {
        $data = $request->validated();
        $data['status'] = 'direncanakan';
        $data['unit_id'] = auth()->user()->unit_id;
        FarmingPlan::create($data);
        return redirect()->route('farming-plans.index')->with('success', 'Rencana kerja berhasil dibuat');
    }

    public function show(FarmingPlan $farmingPlan)
    {
        $this->authorizeUnitModel($farmingPlan);
        $farmingPlan->load(['landPlot', 'cropType', 'foodProgram', 'activities', 'harvests', 'participants.wbp', 'statusHistories.changedBy']);
        // Dropdown WBP PIC harus ber-scope unit rencana (hindari bocor lintas unit)
        $wbpsQuery = \App\Models\Wbp::where('status', 'aktif');
        if ($farmingPlan->unit_id) $wbpsQuery->where('unit_id', $farmingPlan->unit_id);
        $wbps = $wbpsQuery->orderBy('nama_lengkap')->get();
        return view('farming.show', compact('farmingPlan', 'wbps'));
    }

    public function edit(FarmingPlan $farmingPlan)
    {
        $this->authorizeUnitModel($farmingPlan);
        $programs = FoodProgram::where('jenis_program', 'pertanian')->whereIn('status', ['aktif', 'perencanaan'])->orderBy('nama_program')->get();
        $landPlots = LandPlot::orderBy('nama_lahan')->get();
        $cropTypes = CropType::orderBy('nama_tanaman')->get();
        return view('farming.edit', compact('farmingPlan', 'programs', 'landPlots', 'cropTypes'));
    }

    public function update(UpdateFarmingPlanRequest $request, FarmingPlan $farmingPlan)
    {
        $this->authorizeUnitModel($farmingPlan);
        $farmingPlan->update($this->enforceUnitId($request->validated()));
        return redirect()->route('farming-plans.index')->with('success', 'Rencana kerja berhasil diperbarui');
    }

    public function destroy(FarmingPlan $farmingPlan)
    {
        $this->authorizeUnitModel($farmingPlan);
        $farmingPlan->delete();
        return redirect()->route('farming-plans.index')->with('success', 'Rencana kerja berhasil dihapus');
    }

    // === FARMING ACTIVITIES ===
    public function storeActivity(Request $request, FarmingPlan $farmingPlan)
    {
        $request->validate([
            'jenis_kegiatan' => 'required|in:pengolahan_tanah,penanaman,pemupukan,penyiraman,penyiangan,hama_panen,perawatan_lain',
            'tanggal_kegiatan' => 'required|date',
            'pic_wbp_id' => 'nullable|exists:wbp,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        FarmingActivity::create(array_merge(
            $request->only(['jenis_kegiatan', 'tanggal_kegiatan', 'biaya', 'pic_wbp_id', 'keterangan']),
            ['farming_plan_id' => $farmingPlan->id]
        ));

        return back()->with('success', 'Kegiatan pertanian berhasil dicatat!');
    }

    // === HARVESTS ===
    public function storeHarvest(Request $request, FarmingPlan $farmingPlan)
    {
        $request->validate([
            'tanggal_panen' => 'required|date',
            'total_berat_kg' => 'required|numeric|min:0.01',
            'kualitas' => 'required|in:premium,baik,cukup,rendah',
        ]);

        $totalPendapatan = ($request->total_berat_kg ?? 0) * ($request->harga_jual_per_kg ?? 0);

        Harvest::create(array_merge(
            $request->only(['tanggal_panen', 'total_berat_kg', 'kualitas', 'harga_jual_per_kg', 'keterangan']),
            [
                'kode_panen' => 'PNN-' . strtoupper(uniqid()),
                'farming_plan_id' => $farmingPlan->id,
                'unit_id' => auth()->user()->unit_id,
                'total_pendapatan' => $totalPendapatan,
            ]
        ));

        return back()->with('success', 'Data panen berhasil dicatat!');
    }

    /**
     * Ubah status rencana pertanian (aksi cepat dari halaman detail).
     */
    public function updateStatus(Request $request, FarmingPlan $farmingPlan)
    {
        $this->authorizeUnitModel($farmingPlan);
        $request->validate(['status' => 'required|in:direncanakan,proses_tanam,tumbuh,siap_panen,panen,selesai']);
        $farmingPlan->update(['status' => $request->status]);
        return back()->with('success', 'Status rencana pertanian diperbarui menjadi ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }
}

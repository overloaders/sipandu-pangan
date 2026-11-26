<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsIndicator;
use App\Models\AnalyticsTarget;
use App\Models\Unit;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreAnalyticsIndicatorRequest;
use App\Http\Requests\Web\UpdateAnalyticsIndicatorRequest;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
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
        $query = AnalyticsIndicator::with(['targets.unit']);
        $this->applyUnitScope($query, $request);

        if ($request->kategori) $query->where('kategori', $request->kategori);
        if ($request->status) {
            $query->where('is_active', $request->status === 'aktif');
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_indikator', 'like', "%{$request->search}%")
                  ->orWhere('nama_indikator', 'like', "%{$request->search}%");
            });
        }

        $analytics = $query->orderByDesc('created_at')->paginate(15);
        return view('analytics.index', compact('analytics'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        return view('analytics.create', compact('units'));
    }

    public function store(StoreAnalyticsIndicatorRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_indikator' => 'required|unique:analytics_indicators,kode_indikator',
            'nama_indikator' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'target_nilai' => 'required|numeric',
        ]);

        AnalyticsIndicator::create(array_merge(
            $request->only([
                'kode_indikator', 'nama_indikator', 'kategori', 'deskripsi',
                'rumus_perhitungan', 'satuan', 'target_nilai', 'batas_minimum',
                'batas_maksimum', 'unit_id',
            ]),
            ['is_active' => true]
        ));

        return redirect()->route('analytics.index')->with('success', 'Indikator analitik berhasil ditambahkan!');
    }

    public function show(AnalyticsIndicator $analytic)
    {
        $this->authorizeUnitModel($analytic);
        $analytic->load(['targets.unit']);
        $units = $this->allowedUnitsQuery()->get();
        return view('analytics.show', compact('analytic', 'units'));
    }

    public function edit(AnalyticsIndicator $analytic)
    {
        $this->authorizeUnitModel($analytic);
        $units = $this->allowedUnitsQuery()->get();
        return view('analytics.edit', compact('analytic', 'units'));
    }

    public function update(UpdateAnalyticsIndicatorRequest $request, AnalyticsIndicator $analytic)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($analytic);
        $request->validate([
            'nama_indikator' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'target_nilai' => 'required|numeric',
        ]);

        $data = $request->only([
            'nama_indikator', 'kategori', 'deskripsi', 'rumus_perhitungan',
            'satuan', 'target_nilai', 'batas_minimum', 'batas_maksimum', 'unit_id',
        ]);
        $data['is_active'] = $request->input('is_active', $analytic->is_active);

        $analytic->update($data);

        return redirect()->route('analytics.index')->with('success', 'Indikator analitik berhasil diupdate!');
    }

    public function destroy(AnalyticsIndicator $analytic)
    {
        $this->authorizeUnitModel($analytic);
        $analytic->delete();
        return redirect()->route('analytics.index')->with('success', 'Indikator analitik berhasil dihapus!');
    }

    /**
     * Hitung persentase capaian & status berdasarkan target vs realisasi.
     */
    private function computeCapaian(float $target, float $realisasi): array
    {
        if ($target <= 0) {
            return ['persentase_capaian' => 0, 'status_capaian' => 'sesuai_target'];
        }
        $pct = round($realisasi / $target * 100, 2);
        $status = $realisasi > $target
            ? 'melebihi_target'
            : ($realisasi < $target ? 'di_bawah_target' : 'sesuai_target');
        return ['persentase_capaian' => $pct, 'status_capaian' => $status];
    }

    public function storeTarget(Request $request, AnalyticsIndicator $analytic)
    {
        $this->authorizeUnitModel($analytic);
        $validated = $request->validate([
            'periode_tahun' => 'required|integer|min:2000|max:2100',
            'periode_bulan' => 'nullable|integer|between:1,12',
            'periode_triwulan' => 'nullable|integer|between:1,4',
            'target_nilai' => 'required|numeric|min:0',
            'realisasi_nilai' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $unitId = $this->resolveUnitId($request->unit_id) ?? $analytic->unit_id;
        if (!$unitId) {
            return back()->withErrors(['unit_id' => 'Unit wajib dipilih.'])->withInput();
        }

        $exists = AnalyticsTarget::where('indicator_id', $analytic->id)
            ->where('unit_id', $unitId)
            ->where('periode_tahun', $validated['periode_tahun'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['periode_tahun' => 'Target untuk tahun tersebut sudah ada untuk unit ini.'])->withInput();
        }

        $validated['indicator_id'] = $analytic->id;
        $validated['unit_id'] = $unitId;
        $validated['realisasi_nilai'] = $validated['realisasi_nilai'] ?? 0;
        $validated = array_merge($validated, $this->computeCapaian(
            (float) $validated['target_nilai'], (float) $validated['realisasi_nilai']
        ));

        AnalyticsTarget::create($validated);
        return back()->with('success', 'Target periodik berhasil ditambahkan!');
    }

    public function updateTarget(Request $request, AnalyticsIndicator $analytic, AnalyticsTarget $target)
    {
        $this->authorizeUnitModel($analytic);
        if ($target->indicator_id !== $analytic->id) {
            abort(404);
        }
        $validated = $request->validate([
            'periode_tahun' => 'required|integer|min:2000|max:2100',
            'periode_bulan' => 'nullable|integer|between:1,12',
            'periode_triwulan' => 'nullable|integer|between:1,4',
            'target_nilai' => 'required|numeric|min:0',
            'realisasi_nilai' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $unitId = $this->resolveUnitId($request->unit_id) ?? $target->unit_id;
        $exists = AnalyticsTarget::where('indicator_id', $analytic->id)
            ->where('unit_id', $unitId)
            ->where('periode_tahun', $validated['periode_tahun'])
            ->where('id', '!=', $target->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['periode_tahun' => 'Target untuk tahun tersebut sudah ada untuk unit ini.'])->withInput();
        }

        $validated['unit_id'] = $unitId;
        $validated['realisasi_nilai'] = $validated['realisasi_nilai'] ?? 0;
        $validated = array_merge($validated, $this->computeCapaian(
            (float) $validated['target_nilai'], (float) $validated['realisasi_nilai']
        ));

        $target->update($validated);
        return back()->with('success', 'Target periodik berhasil diupdate!');
    }

    public function destroyTarget(AnalyticsIndicator $analytic, AnalyticsTarget $target)
    {
        $this->authorizeUnitModel($analytic);
        if ($target->indicator_id !== $analytic->id) {
            abort(404);
        }
        $target->delete();
        return back()->with('success', 'Target periodik berhasil dihapus!');
    }

    public function dashboard(\Illuminate\Http\Request $request)
    {
        // Unit filter for super-admin
        $user = auth()->user();
        $units = null;
        $selectedUnitId = null;

        if ($user->hasRole('super-admin')) {
            $units = $this->allowedUnitsQuery()->get();
            if ($request->filled('unit_id')) {
                $selectedUnitId = (int) $request->unit_id;
            }
        } else {
            $selectedUnitId = $user->unit_id ?? null;
        }

        $query = AnalyticsIndicator::where('is_active', true)->with(['targets']);
        $indicators = $query->get();

        // If a specific unit is selected, filter targets per unit
        if ($selectedUnitId) {
            foreach ($indicators as $indicator) {
                $indicator->setRelation(
                    'targets',
                    $indicator->targets->filter(fn($t) => $t->unit_id == $selectedUnitId)
                );
            }
        }

        $unitName = $selectedUnitId
            ? (\App\Models\Unit::find($selectedUnitId)?->nama_unit ?? 'Semua Unit')
            : 'Semua Unit';

        return view('analytics.dashboard', compact('indicators', 'units', 'selectedUnitId', 'unitName'));
    }
}

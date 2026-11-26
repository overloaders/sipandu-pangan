<?php

namespace App\Http\Controllers;

use App\Models\LandPlot;
use App\Models\Unit;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreLandPlotRequest;
use App\Http\Requests\Web\UpdateLandPlotRequest;
use Illuminate\Http\Request;

class LandPlotController extends Controller
{
    use UnitScope;

    public function index(Request $request)
    {
        $query = LandPlot::with(['unit', 'farmingPlans']);
        $this->applyUnitScope($query, $request);

        if ($request->status) $query->where('status', $request->status);
        if ($request->jenis_tanah) $query->where('jenis_tanah', $request->jenis_tanah);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_lahan', 'like', "%{$request->search}%")
                  ->orWhere('nama_lahan', 'like', "%{$request->search}%");
            });
        }

        $landPlots = $query->orderBy('nama_lahan')->paginate(15);
        return view('land-plots.index', compact('landPlots'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        return view('land-plots.create', compact('units'));
    }

    public function store(StoreLandPlotRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_lahan' => 'required|unique:land_plots,kode_lahan',
            'nama_lahan' => 'required',
            'unit_id' => 'required|exists:units,id',
            'luas_m2' => 'required|numeric|min:0',
            'status' => 'required',
        ]);

        LandPlot::create($request->only([
            'kode_lahan', 'nama_lahan', 'unit_id', 'luas_m2',
            'jenis_tanah', 'sistem_irigasi', 'status',
            'latitude', 'longitude', 'keterangan',
        ]));

        return redirect()->route('land-plots.index')->with('success', 'Lahan berhasil ditambahkan!');
    }

    public function show(LandPlot $landPlot)
    {
        $this->authorizeUnitModel($landPlot);
        $landPlot->load(['unit', 'farmingPlans', 'statusHistories.changedBy']);
        return view('land-plots.show', compact('landPlot'));
    }

    public function edit(LandPlot $landPlot)
    {
        $this->authorizeUnitModel($landPlot);
        $units = $this->allowedUnitsQuery()->get();
        return view('land-plots.edit', compact('landPlot', 'units'));
    }

    public function update(UpdateLandPlotRequest $request, LandPlot $landPlot)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($landPlot);
        $request->validate([
            'nama_lahan' => 'required',
            'unit_id' => 'required|exists:units,id',
            'luas_m2' => 'required|numeric|min:0',
            'status' => 'required',
        ]);

        $landPlot->update($request->only([
            'nama_lahan', 'unit_id', 'luas_m2',
            'jenis_tanah', 'sistem_irigasi', 'status',
            'latitude', 'longitude', 'keterangan',
        ]));

        return redirect()->route('land-plots.index')->with('success', 'Lahan berhasil diupdate!');
    }

    public function destroy(LandPlot $landPlot)
    {
        $this->authorizeUnitModel($landPlot);
        $landPlot->delete();
        return redirect()->route('land-plots.index')->with('success', 'Lahan berhasil dihapus!');
    }

    /**
     * Ubah status lahan (aksi cepat dari halaman detail).
     */
    public function updateStatus(Request $request, LandPlot $landPlot)
    {
        $this->authorizeUnitModel($landPlot);
        $request->validate(['status' => 'required|in:tersedia,ditanami,istirahat,renovasi,tidak_digunakan']);
        $landPlot->update(['status' => $request->status]);
        return back()->with('success', 'Status lahan diperbarui menjadi ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }
}

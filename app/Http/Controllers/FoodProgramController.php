<?php

namespace App\Http\Controllers;

use App\Models\FoodProgram;
use App\Models\Unit;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreFoodProgramRequest;
use App\Http\Requests\Web\UpdateFoodProgramRequest;

class FoodProgramController extends Controller
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
        $query = FoodProgram::with(['unit', 'creator', 'farmingPlans.participants', 'livestockBarns.participants', 'fishPonds.participants']);
        $this->applyUnitScope($query, $request);
        if ($search = $request->input('search')) {
            $query->where('nama_program', 'LIKE', "%{$search}%");
        }
        if ($request->jenis_program) $query->where('jenis_program', $request->jenis_program);
        if ($request->from_date) $query->where('tanggal_mulai', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_mulai', '<=', $request->to_date);
        $programs = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Statistik (ber-scope unit) — dihitung di controller agar tidak query di view & tidak bocor lintas unit
        $statTotal = FoodProgram::query();
        $this->applyUnitScope($statTotal, $request);
        $statAktif = FoodProgram::where('status', 'aktif');
        $this->applyUnitScope($statAktif, $request);
        $statPerencanaan = FoodProgram::where('status', 'perencanaan');
        $this->applyUnitScope($statPerencanaan, $request);
        $statSelesai = FoodProgram::where('status', 'selesai');
        $this->applyUnitScope($statSelesai, $request);
        $statJenis = FoodProgram::selectRaw('jenis_program, count(*) as total')->groupBy('jenis_program');
        $this->applyUnitScope($statJenis, $request);

        $stats = [
            'total' => $statTotal->count(),
            'aktif' => $statAktif->count(),
            'perencanaan' => $statPerencanaan->count(),
            'selesai' => $statSelesai->count(),
            'jenis' => $statJenis->pluck('total', 'jenis_program'),
        ];
        return view('food-programs.index', compact('programs', 'stats'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        return view('food-programs.create', compact('units'));
    }

    public function store(StoreFoodProgramRequest $request)
    {
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        $data['created_by'] = auth()->id();
        $data['status'] = 'perencanaan';
        FoodProgram::create($data);
        return redirect()->route('food-programs.index')->with('success', 'Program berhasil dibuat');
    }

    public function show(FoodProgram $foodProgram)
    {
        $this->authorizeUnitModel($foodProgram);
        $foodProgram->load(['unit', 'creator', 'farmingPlans.participants.wbp', 'livestockBarns', 'fishPonds', 'budgets', 'statusHistories.changedBy']);
        return view('food-programs.show', compact('foodProgram'));
    }

    public function edit(FoodProgram $foodProgram)
    {
        $this->authorizeUnitModel($foodProgram);
        if (in_array($foodProgram->status, ['selesai', 'dibatalkan'])) {
            return redirect()->route('food-programs.show', $foodProgram->id)
                ->with('error', 'Program dengan status "' . ucfirst($foodProgram->status) . '" tidak dapat diedit.');
        }
        $units = $this->allowedUnitsQuery()->get();
        return view('food-programs.edit', compact('foodProgram', 'units'));
    }

    public function update(UpdateFoodProgramRequest $request, FoodProgram $foodProgram)
    {
        $this->authorizeUnitModel($foodProgram);
        if (in_array($foodProgram->status, ['selesai', 'dibatalkan'])) {
            return redirect()->route('food-programs.show', $foodProgram->id)
                ->with('error', 'Program dengan status "' . ucfirst($foodProgram->status) . '" tidak dapat diupdate.');
        }
        $foodProgram->update($this->enforceUnitId($request->validated()));
        return redirect()->route('food-programs.index')->with('success', 'Program berhasil diperbarui');
    }

    public function destroy(FoodProgram $foodProgram)
    {
        $this->authorizeUnitModel($foodProgram);
        $foodProgram->delete();
        return redirect()->route('food-programs.index')->with('success', 'Program berhasil dihapus');
    }

    /**
     * Ubah status program pangan (aksi cepat dari halaman detail).
     * Status selesai / dibatalkan bersifat final.
     */
    public function updateStatus(Request $request, FoodProgram $foodProgram)
    {
        $this->authorizeUnitModel($foodProgram);
        $request->validate(['status' => 'required|in:perencanaan,aktif,selesai,dibatalkan']);
        if (in_array($foodProgram->status, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Program dengan status "' . ucfirst($foodProgram->status) . '" tidak dapat diubah.');
        }
        $foodProgram->update(['status' => $request->status]);
        return back()->with('success', 'Status program diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

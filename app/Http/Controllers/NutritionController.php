<?php

namespace App\Http\Controllers;

use App\Models\NutritionPlan;
use App\Models\MealComponent;
use App\Models\InventoryItem;
use App\Models\FoodProgram;
use App\Models\Unit;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreNutritionPlanRequest;
use App\Http\Requests\Web\UpdateNutritionPlanRequest;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    use UnitScope;

    public function index(Request $request)
    {
        $query = NutritionPlan::with(['unit']);
        $this->applyUnitScope($query, $request);

        if ($request->status) $query->where('status', $request->status);
        if ($request->tipe_porsi) $query->where('tipe_porsi', $request->tipe_porsi);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_rencana_gizi', 'like', "%{$request->search}%")
                  ->orWhere('nama_menu', 'like', "%{$request->search}%");
            });
        }

        $plans = $query->orderByDesc('created_at')->paginate(15);
        return view('nutrition.index', compact('plans'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        return view('nutrition.create', compact('units'));
    }

    public function store(StoreNutritionPlanRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_rencana_gizi' => 'required|unique:nutrition_plans,kode_rencana_gizi',
            'nama_menu' => 'required',
            'unit_id' => 'required|exists:units,id',
            'waktu_makan' => 'required',
            'tipe_porsi' => 'required',
            'status' => 'required',
        ]);

        NutritionPlan::create($request->only([
            'kode_rencana_gizi', 'nama_menu', 'unit_id', 'waktu_makan',
            'berlaku_mulai', 'berlaku_selesai', 'kalori_target_kcal',
            'protein_target_gr', 'karbohidrat_target_gr', 'lemak_target_gr',
            'serat_target_gr', 'tipe_porsi', 'estimasi_biaya_per_porsi',
            'sumber_bahan', 'deskripsi', 'status',
        ]));

        return redirect()->route('nutrition.index')->with('success', 'Rencana gizi berhasil ditambahkan!');
    }

    public function show(NutritionPlan $nutrition)
    {
        $this->authorizeUnitModel($nutrition);
        $nutrition->load(['unit', 'components.item', 'statusHistories.changedBy']);
        // Bahan baku untuk dropdown komponen — scope unit rencana (hindari bocor lintas unit)
        $items = InventoryItem::where('unit_id', $nutrition->unit_id)->orderBy('nama_item')->get();
        return view('nutrition.show', ['plan' => $nutrition, 'items' => $items]);
    }

    public function edit(NutritionPlan $nutrition)
    {
        $this->authorizeUnitModel($nutrition);
        if ($nutrition->status === 'nonaktif') {
            return redirect()->route('nutrition.show', $nutrition->id)
                ->with('error', 'Rencana gizi yang sudah nonaktif tidak dapat diedit.');
        }
        $units = $this->allowedUnitsQuery()->get();
        return view('nutrition.edit', ['plan' => $nutrition, 'units' => $units]);
    }

    public function update(UpdateNutritionPlanRequest $request, NutritionPlan $nutrition)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($nutrition);
        if ($nutrition->status === 'nonaktif') {
            return redirect()->route('nutrition.show', $nutrition->id)
                ->with('error', 'Rencana gizi yang sudah nonaktif tidak dapat diupdate.');
        }
        $request->validate([
            'nama_menu' => 'required',
            'unit_id' => 'required|exists:units,id',
            'waktu_makan' => 'required',
            'tipe_porsi' => 'required',
            'status' => 'required',
        ]);

        $nutrition->update($request->only([
            'nama_menu', 'unit_id', 'waktu_makan',
            'berlaku_mulai', 'berlaku_selesai', 'kalori_target_kcal',
            'protein_target_gr', 'karbohidrat_target_gr', 'lemak_target_gr',
            'serat_target_gr', 'tipe_porsi', 'estimasi_biaya_per_porsi',
            'sumber_bahan', 'deskripsi', 'status',
        ]));

        return redirect()->route('nutrition.index')->with('success', 'Rencana gizi berhasil diupdate!');
    }

    public function destroy(NutritionPlan $nutrition)
    {
        $this->authorizeUnitModel($nutrition);
        $nutrition->delete();
        return redirect()->route('nutrition.index')->with('success', 'Rencana gizi berhasil dihapus!');
    }

    // === KOMPONEN MENU ===
    public function storeComponent(Request $request, NutritionPlan $nutrition)
    {
        $this->authorizeUnitModel($nutrition);
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:30',
            'kalori_kcal' => 'nullable|numeric',
            'protein_gr' => 'nullable|numeric',
            'karbohidrat_gr' => 'nullable|numeric',
            'lemak_gr' => 'nullable|numeric',
            'keterangan' => 'nullable|string|max:1000',
        ]);
        $data['nutrition_plan_id'] = $nutrition->id;
        $data['satuan'] = $data['satuan'] ?? 'gram';
        $data['urutan'] = $nutrition->components()->count();
        MealComponent::create($data);
        return back()->with('success', 'Komponen menu berhasil ditambahkan');
    }

    public function updateComponent(Request $request, NutritionPlan $nutrition, MealComponent $component)
    {
        $this->authorizeUnitModel($nutrition);
        $data = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:30',
            'kalori_kcal' => 'nullable|numeric',
            'protein_gr' => 'nullable|numeric',
            'karbohidrat_gr' => 'nullable|numeric',
            'lemak_gr' => 'nullable|numeric',
            'keterangan' => 'nullable|string|max:1000',
        ]);
        $component->update($data);
        return back()->with('success', 'Komponen menu berhasil diperbarui');
    }

    public function destroyComponent(NutritionPlan $nutrition, MealComponent $component)
    {
        $this->authorizeUnitModel($nutrition);
        $component->delete();
        return back()->with('success', 'Komponen menu berhasil dihapus');
    }

    /**
     * Ubah status rencana gizi (aksi cepat dari halaman detail).
     * Status nonaktif bersifat final.
     */
    public function updateStatus(Request $request, NutritionPlan $nutrition)
    {
        $this->authorizeUnitModel($nutrition);
        $request->validate(['status' => 'required|in:draft,aktif,nonaktif']);
        if ($nutrition->status === 'nonaktif') {
            return back()->with('error', 'Rencana gizi yang sudah nonaktif tidak dapat diubah statusnya.');
        }
        $nutrition->update(['status' => $request->status]);
        return back()->with('success', 'Status rencana gizi diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

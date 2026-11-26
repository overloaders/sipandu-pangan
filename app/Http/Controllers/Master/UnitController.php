<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->canManageMaster()) {
                abort(403, 'Anda tidak memiliki akses ke data master.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Unit::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_unit', 'LIKE', "%{$search}%")->orWhere('kode_unit', 'LIKE', "%{$search}%");
            });
        }
        $units = $query->orderBy('kode_unit')->paginate(15)->withQueryString();
        return view('master.units.index', compact('units'));
    }

    public function create()
    {
        $parentUnits = Unit::whereNull('parent_id')->orderBy('nama_unit')->get();
        return view('master.units.create', compact('parentUnits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_unit' => 'required|unique:units,kode_unit',
            'nama_unit' => 'required|string|max:200',
            'tipe_unit' => 'required',
            'status' => 'required',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);
        $data = $request->only([
            'kode_unit', 'nama_unit', 'tipe_unit', 'parent_id', 'provinsi', 'kota_kab',
            'alamat', 'telepon', 'email', 'kapasitas_wbp', 'status',
        ]);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('units', 'public');
        }
        Unit::create($data);
        return redirect()->route('master.units.index')->with('success', 'Unit berhasil ditambahkan!');
    }

    public function show(Unit $unit)
    {
        $unit->load(['children', 'wbps']);
        return view('master.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $parentUnits = Unit::whereNull('parent_id')->where('id', '!=', $unit->id)->orderBy('nama_unit')->get();
        return view('master.units.edit', compact('unit', 'parentUnits'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:200',
            'tipe_unit' => 'required',
            'status' => 'required',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);
        $data = $request->only([
            'nama_unit', 'tipe_unit', 'parent_id', 'provinsi', 'kota_kab',
            'alamat', 'telepon', 'email', 'kapasitas_wbp', 'status',
        ]);
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($unit->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($unit->logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($unit->logo);
            }
            $data['logo'] = $request->file('logo')->store('units', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($unit->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($unit->logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($unit->logo);
            }
            $data['logo'] = null;
        }
        $unit->update($data);
        return redirect()->route('master.units.index')->with('success', 'Unit berhasil diupdate!');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('master.units.index')->with('success', 'Unit berhasil dihapus!');
    }
}

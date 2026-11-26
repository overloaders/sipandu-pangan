<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\LivestockType;
use App\Http\Requests\Web\StoreLivestockTypeRequest;
use App\Http\Requests\Web\UpdateLivestockTypeRequest;
use Illuminate\Http\Request;

class LivestockTypeController extends Controller
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
        $query = LivestockType::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_jenis', 'LIKE', "%{$search}%")->orWhere('kode_jenis', 'LIKE', "%{$search}%");
            });
        }
        $types = $query->orderBy('kode_jenis')->paginate(15)->withQueryString();
        return view('master.livestock-types.index', compact('types'));
    }

    public function create()
    {
        return view('master.livestock-types.create');
    }

    public function store(StoreLivestockTypeRequest $request)
    {
        $request->validate([
            'kode_jenis' => 'required|unique:livestock_types,kode_jenis',
            'nama_jenis' => 'required|string|max:100',
            'kategori' => 'required',
        ]);
        LivestockType::create($request->only([
            'kode_jenis', 'nama_jenis', 'kategori', 'berat_rata_kg', 'harga_beli_per_ekor',
            'harga_jual_per_kg', 'masa_pemeliharaan_hari', 'pakan_utama', 'keterangan',
        ]));
        return redirect()->route('master.livestock-types.index')->with('success', 'Jenis ternak berhasil ditambahkan!');
    }

    public function show(LivestockType $livestockType)
    {
        return view('master.livestock-types.show', compact('livestockType'));
    }

    public function edit(LivestockType $livestockType)
    {
        return view('master.livestock-types.edit', compact('livestockType'));
    }

    public function update(UpdateLivestockTypeRequest $request, LivestockType $livestockType)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:100',
            'kategori' => 'required',
        ]);
        $livestockType->update($request->only([
            'nama_jenis', 'kategori', 'berat_rata_kg', 'harga_beli_per_ekor',
            'harga_jual_per_kg', 'masa_pemeliharaan_hari', 'pakan_utama', 'keterangan',
        ]));
        return redirect()->route('master.livestock-types.index')->with('success', 'Jenis ternak berhasil diupdate!');
    }

    public function destroy(LivestockType $livestockType)
    {
        $livestockType->delete();
        return redirect()->route('master.livestock-types.index')->with('success', 'Jenis ternak berhasil dihapus!');
    }
}

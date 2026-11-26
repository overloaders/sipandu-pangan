<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\CropType;
use App\Http\Requests\Web\StoreCropTypeRequest;
use App\Http\Requests\Web\UpdateCropTypeRequest;
use Illuminate\Http\Request;

class CropTypeController extends Controller
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
        $query = CropType::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_tanaman', 'LIKE', "%{$search}%")->orWhere('kode_tanaman', 'LIKE', "%{$search}%");
            });
        }
        $types = $query->orderBy('kode_tanaman')->paginate(15)->withQueryString();
        return view('master.crop-types.index', compact('types'));
    }

    public function create()
    {
        return view('master.crop-types.create');
    }

    public function store(StoreCropTypeRequest $request)
    {
        $request->validate([
            'kode_tanaman' => 'required|unique:crop_types,kode_tanaman',
            'nama_tanaman' => 'required|string|max:100',
            'kategori' => 'required',
        ]);
        CropType::create($request->only([
            'kode_tanaman', 'nama_tanaman', 'nama_latin', 'kategori', 'masa_tanam_hari',
            'hasil_per_m2_kg', 'harga_jual_per_kg', 'musim_tanam', 'keterangan',
        ]));
        return redirect()->route('master.crop-types.index')->with('success', 'Jenis tanaman berhasil ditambahkan!');
    }

    public function show(CropType $cropType)
    {
        return view('master.crop-types.show', compact('cropType'));
    }

    public function edit(CropType $cropType)
    {
        return view('master.crop-types.edit', compact('cropType'));
    }

    public function update(UpdateCropTypeRequest $request, CropType $cropType)
    {
        $request->validate([
            'nama_tanaman' => 'required|string|max:100',
            'kategori' => 'required',
        ]);
        $cropType->update($request->only([
            'nama_tanaman', 'nama_latin', 'kategori', 'masa_tanam_hari',
            'hasil_per_m2_kg', 'harga_jual_per_kg', 'musim_tanam', 'keterangan',
        ]));
        return redirect()->route('master.crop-types.index')->with('success', 'Jenis tanaman berhasil diupdate!');
    }

    public function destroy(CropType $cropType)
    {
        $cropType->delete();
        return redirect()->route('master.crop-types.index')->with('success', 'Jenis tanaman berhasil dihapus!');
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Http\Requests\Web\StoreInventoryCategoryRequest;
use App\Http\Requests\Web\UpdateInventoryCategoryRequest;
use Illuminate\Http\Request;

class InventoryCategoryController extends Controller
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
        $query = InventoryCategory::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'LIKE', "%{$search}%")->orWhere('kode_kategori', 'LIKE', "%{$search}%");
            });
        }
        $categories = $query->orderBy('kode_kategori')->paginate(15)->withQueryString();
        return view('master.inventory-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('master.inventory-categories.create');
    }

    public function store(StoreInventoryCategoryRequest $request)
    {
        $request->validate([
            'kode_kategori' => 'required|unique:inventory_categories,kode_kategori',
            'nama_kategori' => 'required|string|max:100',
            'tipe' => 'required',
        ]);
        InventoryCategory::create($request->only([
            'kode_kategori', 'nama_kategori', 'tipe', 'satuan_standar', 'keterangan',
        ]));
        return redirect()->route('master.inventory-categories.index')->with('success', 'Kategori inventaris berhasil ditambahkan!');
    }

    public function show(InventoryCategory $inventoryCategory)
    {
        return view('master.inventory-categories.show', compact('inventoryCategory'));
    }

    public function edit(InventoryCategory $inventoryCategory)
    {
        return view('master.inventory-categories.edit', compact('inventoryCategory'));
    }

    public function update(UpdateInventoryCategoryRequest $request, InventoryCategory $inventoryCategory)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'tipe' => 'required',
        ]);
        $inventoryCategory->update($request->only([
            'nama_kategori', 'tipe', 'satuan_standar', 'keterangan',
        ]));
        return redirect()->route('master.inventory-categories.index')->with('success', 'Kategori inventaris berhasil diupdate!');
    }

    public function destroy(InventoryCategory $inventoryCategory)
    {
        $inventoryCategory->delete();
        return redirect()->route('master.inventory-categories.index')->with('success', 'Kategori inventaris berhasil dihapus!');
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\FinancialCategory;
use App\Http\Requests\Web\StoreFinancialCategoryRequest;
use App\Http\Requests\Web\UpdateFinancialCategoryRequest;
use Illuminate\Http\Request;

class FinancialCategoryController extends Controller
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
        $query = FinancialCategory::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'LIKE', "%{$search}%")->orWhere('kode_kategori_keu', 'LIKE', "%{$search}%");
            });
        }
        $categories = $query->orderBy('kode_kategori_keu')->paginate(15)->withQueryString();
        return view('master.financial-categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = FinancialCategory::whereNull('parent_id')->orderBy('nama_kategori')->get();
        return view('master.financial-categories.create', compact('parents'));
    }

    public function store(StoreFinancialCategoryRequest $request)
    {
        $request->validate([
            'kode_kategori_keu' => 'required|unique:financial_categories,kode_kategori_keu',
            'nama_kategori' => 'required|string|max:100',
            'tipe' => 'required',
        ]);
        FinancialCategory::create($request->only([
            'kode_kategori_keu', 'nama_kategori', 'tipe', 'parent_type', 'parent_id', 'keterangan', 'is_active',
        ]));
        return redirect()->route('master.financial-categories.index')->with('success', 'Kategori keuangan berhasil ditambahkan!');
    }

    public function show(FinancialCategory $financialCategory)
    {
        return view('master.financial-categories.show', compact('financialCategory'));
    }

    public function edit(FinancialCategory $financialCategory)
    {
        $parents = FinancialCategory::whereNull('parent_id')->where('id', '!=', $financialCategory->id)->orderBy('nama_kategori')->get();
        return view('master.financial-categories.edit', compact('financialCategory', 'parents'));
    }

    public function update(UpdateFinancialCategoryRequest $request, FinancialCategory $financialCategory)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'tipe' => 'required',
        ]);
        $financialCategory->update($request->only([
            'nama_kategori', 'tipe', 'parent_type', 'parent_id', 'keterangan', 'is_active',
        ]));
        return redirect()->route('master.financial-categories.index')->with('success', 'Kategori keuangan berhasil diupdate!');
    }

    public function destroy(FinancialCategory $financialCategory)
    {
        $financialCategory->delete();
        return redirect()->route('master.financial-categories.index')->with('success', 'Kategori keuangan berhasil dihapus!');
    }
}

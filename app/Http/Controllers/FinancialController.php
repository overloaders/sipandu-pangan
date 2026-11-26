<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\FinancialCategory;
use App\Models\Unit;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreFinancialTransactionRequest;
use App\Http\Requests\Web\UpdateFinancialTransactionRequest;

class FinancialController extends Controller
{
    use UnitScope;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->canAccessFinance()) {
                abort(403, 'Anda tidak memiliki akses ke modul keuangan.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy', 'updateStatus']);
    }

    public function index(Request $request)
    {
        $query = FinancialTransaction::with(['category', 'unit']);
        $this->applyUnitScope($query, $request);
        if ($request->tipe) $query->where('tipe', $request->tipe);
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->filled('from_date')) $query->where('tanggal_transaksi', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->where('tanggal_transaksi', '<=', $request->to_date);
        $transactions = $query->orderByDesc('tanggal_transaksi')->paginate(15)->withQueryString();

        // Statistik (ber-scope unit) — dihitung di controller agar tidak query di view & tidak bocor lintas unit
        $startOfMonth = now()->startOfMonth();
        $pemasukanBulan = FinancialTransaction::where('tipe', 'pemasukan')->where('tanggal_transaksi', '>=', $startOfMonth);
        $this->applyUnitScope($pemasukanBulan, $request);
        $pengeluaranBulan = FinancialTransaction::where('tipe', 'pengeluaran')->where('tanggal_transaksi', '>=', $startOfMonth);
        $this->applyUnitScope($pengeluaranBulan, $request);
        $pemasukanTotal = FinancialTransaction::where('tipe', 'pemasukan');
        $this->applyUnitScope($pemasukanTotal, $request);
        $pengeluaranTotal = FinancialTransaction::where('tipe', 'pengeluaran');
        $this->applyUnitScope($pengeluaranTotal, $request);

        $stats = [
            'pemasukan_bulan' => (float) $pemasukanBulan->sum('jumlah'),
            'pengeluaran_bulan' => (float) $pengeluaranBulan->sum('jumlah'),
            'saldo' => (float) $pemasukanTotal->sum('jumlah') - (float) $pengeluaranTotal->sum('jumlah'),
        ];
        return view('financial.index', compact('transactions', 'stats'));
    }

    public function create()
    {
        $categories = FinancialCategory::orderBy('nama_kategori')->get();
        $units = $this->allowedUnitsQuery()->get();
        return view('financial.create', compact('categories', 'units'));
    }

    public function store(StoreFinancialTransactionRequest $request)
    {
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        $data['kode_transaksi_keu'] = 'FIN-' . now()->format('Ymd-His');
        $data['status'] = 'draft';
        FinancialTransaction::create($data);
        return redirect()->route('financial.index')->with('success', 'Transaksi keuangan berhasil dicatat');
    }

    public function show(FinancialTransaction $financial)
    {
        $this->authorizeUnitModel($financial);
        $financial->load(['category', 'unit', 'approver', 'statusHistories.changedBy']);
        return view('financial.show', ['transaction' => $financial]);
    }

    public function edit(FinancialTransaction $financial)
    {
        $this->authorizeUnitModel($financial);
        if ($financial->status === 'disetujui') {
            return redirect()->route('financial.show', $financial->id)
                ->with('error', 'Transaksi yang sudah disetujui tidak dapat diedit.');
        }
        $financial->load(['category', 'unit']);
        return view('financial.edit', ['transaction' => $financial]);
    }

    public function update(UpdateFinancialTransactionRequest $request, FinancialTransaction $financial)
    {
        $this->authorizeUnitModel($financial);
        if ($financial->status === 'disetujui') {
            return redirect()->route('financial.show', $financial->id)
                ->with('error', 'Transaksi yang sudah disetujui tidak dapat diupdate.');
        }
        $financial->update($this->enforceUnitId($request->validated()));
        if ($request->status === 'disetujui') $financial->update(['approved_by' => auth()->id()]);
        return redirect()->route('financial.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(FinancialTransaction $financial)
    {
        $this->authorizeUnitModel($financial);
        $financial->delete();
        return redirect()->route('financial.index')->with('success', 'Transaksi berhasil dihapus');
    }

    /**
     * Ubah status transaksi (aksi cepat dari halaman index/show).
     * Transaksi yang sudah disetujui terkunci dan tidak dapat diubah lagi.
     */
    public function updateStatus(Request $request, FinancialTransaction $transaction)
    {
        $this->authorizeUnitModel($transaction);
        $request->validate([
            'status' => 'required|in:draft,pending_approval,disetujui,ditolak,selesai',
        ]);

        if ($transaction->status === 'disetujui' && $request->status !== 'disetujui') {
            return back()->with('error', 'Transaksi yang sudah disetujui tidak dapat diubah statusnya.');
        }

        $data = ['status' => $request->status];
        if ($request->status === 'disetujui') {
            $data['approved_by'] = auth()->id();
        }
        $transaction->update($data);

        return back()->with('success', 'Status transaksi diperbarui menjadi ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }
}

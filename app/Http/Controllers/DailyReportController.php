<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportDetail;
use App\Models\Unit;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreDailyReportRequest;
use App\Http\Requests\Web\UpdateDailyReportRequest;

class DailyReportController extends Controller
{
    use UnitScope;

    public function index(Request $request)
    {
        $query = DailyReport::with(['unit', 'reporter']);
        $this->applyUnitScope($query, $request);
        if ($request->status) $query->where('status', $request->status);
        if ($request->from_date) $query->where('tanggal_laporan', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_laporan', '<=', $request->to_date);
        $reports = $query->orderByDesc('tanggal_laporan')->paginate(15)->withQueryString();
        return view('daily-reports.index', compact('reports'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        return view('daily-reports.create', compact('units'));
    }

    public function store(StoreDailyReportRequest $request)
    {
        $data = $request->validated();
        $data['dilaporkan_oleh'] = auth()->id();
        $data['unit_id'] = auth()->user()->unit_id;
        $data['status'] = 'draft';
        $data['kode_laporan'] = 'LRN-' . now()->format('Ymd-His');
        DailyReport::create($data);
        return redirect()->route('daily-reports.index')->with('success', 'Laporan harian berhasil dibuat');
    }

    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load(['unit', 'reporter', 'confirmer', 'details', 'statusHistories.changedBy']);
        return view('daily-reports.show', compact('dailyReport'));
    }

    public function edit(DailyReport $dailyReport)
    {
        if ($dailyReport->status === 'dikonfirmasi') {
            return redirect()->route('daily-reports.show', $dailyReport->id)
                ->with('error', 'Laporan yang sudah dikonfirmasi tidak dapat diedit.');
        }
        $dailyReport->load(['unit', 'reporter']);
        return view('daily-reports.edit', compact('dailyReport'));
    }

    public function update(UpdateDailyReportRequest $request, DailyReport $dailyReport)
    {
        if ($dailyReport->status === 'dikonfirmasi') {
            return redirect()->route('daily-reports.show', $dailyReport->id)
                ->with('error', 'Laporan yang sudah dikonfirmasi tidak dapat diupdate.');
        }
        $dailyReport->update($request->validated());
        return redirect()->route('daily-reports.index')->with('success', 'Laporan berhasil diperbarui');
    }

    public function destroy(DailyReport $dailyReport)
    {
        $dailyReport->delete();
        return redirect()->route('daily-reports.index')->with('success', 'Laporan berhasil dihapus');
    }

    /**
     * Kirim laporan dari draft → dikirim (untuk review supervisor/admin).
     */
    public function kirim(DailyReport $dailyReport)
    {
        if (!$this->canAccessUnit($dailyReport->unit_id)) {
            $msg = 'Anda tidak memiliki akses ke laporan ini';
            return request()->expectsJson() ? response()->json(['message' => $msg], 403) : back()->with('error', $msg);
        }
        if ($dailyReport->status !== 'draft') {
            $msg = 'Hanya laporan berstatus draft yang bisa dikirim';
            return request()->expectsJson() ? response()->json(['message' => $msg], 422) : back()->with('warning', $msg);
        }

        $dailyReport->update(['status' => 'dikirim']);
        $msg = 'Laporan berhasil dikirim untuk review';
        return request()->expectsJson() ? response()->json(['message' => $msg]) : back()->with('success', $msg);
    }

    /**
     * Konfirmasi laporan dari dikirim → dikonfirmasi (hanya admin/supervisor).
     */
    public function confirm(DailyReport $dailyReport)
    {
        if (!$this->canAccessUnit($dailyReport->unit_id)) {
            $msg = 'Anda tidak memiliki akses ke laporan ini';
            return request()->expectsJson() ? response()->json(['message' => $msg], 403) : back()->with('error', $msg);
        }
        if ($dailyReport->status !== 'dikirim') {
            $msg = 'Hanya laporan berstatus dikirim yang bisa dikonfirmasi';
            return request()->expectsJson() ? response()->json(['message' => $msg], 422) : back()->with('warning', $msg);
        }

        $dailyReport->update([
            'status' => 'dikonfirmasi',
            'dikonfirmasi_oleh' => auth()->id(),
        ]);
        $msg = 'Laporan berhasil dikonfirmasi';
        return request()->expectsJson() ? response()->json(['message' => $msg]) : back()->with('success', $msg);
    }

    /**
     * Tolak kembali laporan dari dikirim → draft (hanya admin/supervisor).
     */
    public function reject(DailyReport $dailyReport)
    {
        if (!$this->canAccessUnit($dailyReport->unit_id)) {
            $msg = 'Anda tidak memiliki akses ke laporan ini';
            return request()->expectsJson() ? response()->json(['message' => $msg], 403) : back()->with('error', $msg);
        }
        if ($dailyReport->status !== 'dikirim') {
            $msg = 'Hanya laporan berstatus dikirim yang bisa ditolak';
            return request()->expectsJson() ? response()->json(['message' => $msg], 422) : back()->with('warning', $msg);
        }

        $dailyReport->update([
            'status' => 'draft',
            'dikonfirmasi_oleh' => null,
        ]);
        $msg = 'Laporan dikembalikan ke draft untuk diperbaiki';
        return request()->expectsJson() ? response()->json(['message' => $msg]) : back()->with('success', $msg);
    }

    // === DETAIL LAPORAN ===
    private function validateDetail(Request $request)
    {
        return $request->validate([
            'kategori_laporan' => 'required|in:pertanian,peternakan,perikanan,inventaris,keuangan,keamanan,kesehatan,pelatihan,lainnya',
            'judul_detail' => 'required|string|max:200',
            'deskripsi' => 'required|string',
            'nilai_angka' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:30',
        ]);
    }

    public function storeDetail(Request $request, DailyReport $dailyReport)
    {
        if (!$this->canAccessUnit($dailyReport->unit_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke laporan ini');
        }
        $dailyReport->details()->create($this->validateDetail($request));
        return back()->with('success', 'Detail laporan berhasil ditambahkan');
    }

    public function updateDetail(Request $request, DailyReport $dailyReport, DailyReportDetail $detail)
    {
        if (!$this->canAccessUnit($dailyReport->unit_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke laporan ini');
        }
        $detail->update($this->validateDetail($request));
        return back()->with('success', 'Detail laporan berhasil diperbarui');
    }

    public function destroyDetail(DailyReport $dailyReport, DailyReportDetail $detail)
    {
        if (!$this->canAccessUnit($dailyReport->unit_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke laporan ini');
        }
        $detail->delete();
        return back()->with('success', 'Detail laporan berhasil dihapus');
    }

    /**
     * Ubah status laporan harian (aksi cepat dari halaman detail).
     * Status dikonfirmasi bersifat final.
     */
    public function updateStatus(Request $request, DailyReport $dailyReport)
    {
        $this->authorizeUnitModel($dailyReport);
        $request->validate(['status' => 'required|in:draft,dikirim,dikonfirmasi,dikembalikan']);
        if ($dailyReport->status === 'dikonfirmasi') {
            return back()->with('error', 'Laporan yang sudah dikonfirmasi tidak dapat diubah statusnya.');
        }
        $dailyReport->update(['status' => $request->status]);
        return back()->with('success', 'Status laporan diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

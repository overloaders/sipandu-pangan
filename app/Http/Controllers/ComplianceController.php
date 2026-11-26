<?php

namespace App\Http\Controllers;

use App\Models\ComplianceRecord;
use App\Models\ComplianceDetail;
use App\Models\Unit;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreComplianceRecordRequest;
use App\Http\Requests\Web\UpdateComplianceRecordRequest;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    use UnitScope;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->hasRole(['super-admin', 'admin', 'supervisor'])) {
                abort(403, 'Anda tidak memiliki akses ke modul ini.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy', 'updateStatus']);
    }

    public function index(Request $request)
    {
        $query = ComplianceRecord::with(['unit', 'inspektur'])->withCount('statusHistories');
        $this->applyUnitScope($query, $request);

        if ($request->status) $query->where('status', $request->status);
        if ($request->tipe) $query->where('tipe', $request->tipe);
        if ($request->from_date) $query->where('tanggal_pemeriksaan', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_pemeriksaan', '<=', $request->to_date);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_komplians', 'like', "%{$request->search}%")
                  ->orWhere('judul', 'like', "%{$request->search}%");
            });
        }

        $records = $query->orderByDesc('tanggal_pemeriksaan')->paginate(15);
        return view('compliance.index', compact('records'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        return view('compliance.create', compact('units'));
    }

    public function store(StoreComplianceRecordRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_komplians' => 'required|unique:compliance_records,kode_komplians',
            'judul' => 'required',
            'tipe' => 'required',
            'unit_id' => 'required|exists:units,id',
            'tanggal_pemeriksaan' => 'required|date',
            'status' => 'required',
        ]);

        $record = ComplianceRecord::create($request->only([
            'kode_komplians', 'judul', 'tipe', 'unit_id',
            'tanggal_pemeriksaan', 'tanggal_berlaku_sampai', 'hasil',
            'nilai_skor', 'tingkat_risiko', 'inspektur_user_id', 'status',
            'temuan', 'rekomendasi',
        ]));

        return redirect()->route('compliance.index')->with('success', 'Catatan komplians berhasil ditambahkan!');
    }

    public function show(ComplianceRecord $compliance)
    {
        $this->authorizeUnitModel($compliance);
        $compliance->load(['details', 'unit', 'inspektur', 'statusHistories.changedBy']);
        return view('compliance.show', ['record' => $compliance]);
    }

    public function edit(ComplianceRecord $compliance)
    {
        $this->authorizeUnitModel($compliance);
        if (in_array($compliance->status, ['kedaluwarsa', 'dibatalkan'])) {
            return redirect()->route('compliance.show', $compliance->id)
                ->with('error', 'Rekaman dengan status "' . ucfirst($compliance->status) . '" tidak dapat diedit.');
        }
        $units = $this->allowedUnitsQuery()->get();
        return view('compliance.edit', ['record' => $compliance, 'units' => $units]);
    }

    public function update(UpdateComplianceRecordRequest $request, ComplianceRecord $compliance)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($compliance);
        if (in_array($compliance->status, ['kedaluwarsa', 'dibatalkan'])) {
            return redirect()->route('compliance.show', $compliance->id)
                ->with('error', 'Rekaman dengan status "' . ucfirst($compliance->status) . '" tidak dapat diupdate.');
        }
        $request->validate([
            'judul' => 'required',
            'tipe' => 'required',
            'unit_id' => 'required|exists:units,id',
            'tanggal_pemeriksaan' => 'required|date',
            'status' => 'required',
        ]);

        $compliance->update($request->only([
            'judul', 'tipe', 'unit_id', 'tanggal_pemeriksaan', 'tanggal_berlaku_sampai',
            'hasil', 'nilai_skor', 'tingkat_risiko', 'inspektur_user_id', 'status',
            'temuan', 'rekomendasi',
        ]));

        return redirect()->route('compliance.index')->with('success', 'Catatan komplians berhasil diupdate!');
    }

    public function destroy(ComplianceRecord $compliance)
    {
        $this->authorizeUnitModel($compliance);
        $compliance->delete();
        return redirect()->route('compliance.index')->with('success', 'Catatan komplians berhasil dihapus!');
    }

    /**
     * Ubah status rekaman komplians (aksi cepat).
     * Status kedaluwarsa / dibatalkan bersifat final dan terkunci.
     */
    public function updateStatus(Request $request, ComplianceRecord $compliance)
    {
        $this->authorizeUnitModel($compliance);
        $request->validate([
            'status' => 'required|in:draft,aktif,perlu_perbaikan,kedaluwarsa,dibatalkan',
        ]);

        if (in_array($compliance->status, ['kedaluwarsa', 'dibatalkan'])) {
            return back()->with('error', 'Rekaman dengan status "' . ucfirst($compliance->status) . '" tidak dapat diubah statusnya.');
        }

        $compliance->update(['status' => $request->status]);
        return back()->with('success', 'Status diperbarui menjadi ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }

    public function details(ComplianceRecord $record)
    {
        $record->load(['details', 'unit']);
        return view('compliance.details', ['record' => $record]);
    }

    public function storeDetail(Request $request, ComplianceRecord $record)
    {
        $this->authorizeUnitModel($record);
        $request->validate([
            'item_pemeriksaan' => 'required',
            'status_pemeriksaan' => 'required|in:memenuhi,tidak_memenuhi,sebagian,tidak_diterapkan',
        ]);

        $record->details()->create($request->only([
            'item_pemeriksaan', 'status_pemeriksaan', 'catatan', 'bobot', 'nilai',
        ]));

        return back()->with('success', 'Detail pemeriksaan berhasil ditambahkan!');
    }

    public function updateDetail(Request $request, ComplianceRecord $record, $detail)
    {
        $this->authorizeUnitModel($record);
        $request->validate([
            'item_pemeriksaan' => 'required',
            'status_pemeriksaan' => 'required|in:memenuhi,tidak_memenuhi,sebagian,tidak_diterapkan',
        ]);

        $d = \App\Models\ComplianceDetail::where('compliance_id', $record->id)->where('id', $detail)->firstOrFail();
        $d->update($request->only([
            'item_pemeriksaan', 'status_pemeriksaan', 'catatan', 'bobot', 'nilai',
        ]));

        return back()->with('success', 'Detail pemeriksaan berhasil diupdate!');
    }

    public function destroyDetail(ComplianceRecord $record, $detail)
    {
        $this->authorizeUnitModel($record);
        \App\Models\ComplianceDetail::where('compliance_id', $record->id)->where('id', $detail)->delete();
        return back()->with('success', 'Detail berhasil dihapus!');
    }
}

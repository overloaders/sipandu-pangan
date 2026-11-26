<?php

namespace App\Http\Controllers;

use App\Models\FishPond;
use App\Models\FishStock;
use App\Models\Unit;
use App\Models\User;
use App\Models\WaterQualityRecord;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreFishPondRequest;
use App\Http\Requests\Web\UpdateFishPondRequest;

class FishPondController extends Controller
{
    use UnitScope;
    public function index(Request $request)
    {
        $query = FishPond::with(['unit', 'fishStocks']);
        $this->applyUnitScope($query, $request);
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kolam', 'LIKE', "%{$search}%")->orWhere('kode_kolam', 'LIKE', "%{$search}%");
            });
        }
        $ponds = $query->orderBy('kode_kolam')->paginate(15)->withQueryString();

        // Statistik (ber-scope unit) — dihitung di controller agar tidak query di view & tidak bocor lintas unit
        $pondAktif = FishPond::where('status', 'aktif');
        $this->applyUnitScope($pondAktif, $request);
        $stokHidup = FishStock::where('status', 'hidup');
        $this->applyUnitScope($stokHidup, $request);
        $kualitasHariIni = WaterQualityRecord::where('tanggal_pemeriksaan', now()->toDateString());
        $this->scopeByPondUnit($kualitasHariIni, $request);
        $panenBulanIni = \App\Models\FishHarvest::where('tanggal_panen', '>=', now()->startOfMonth());
        $this->scopeByPondUnit($panenBulanIni, $request);

        $stats = [
            'kolam_aktif' => $pondAktif->count(),
            'stok_hidup' => (float) $stokHidup->sum('jumlah'),
            'kualitas_hari_ini' => $kualitasHariIni->count(),
            'panen_berat' => (float) $panenBulanIni->sum('total_berat_kg'),
        ];
        return view('fishing.index', compact('ponds', 'stats'));
    }

    /**
     * Scope query tabel anak (tanpa kolom unit_id) ke unit via relasi pond.
     */
    private function scopeByPondUnit($query, Request $request)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('super-admin')) {
            if ($request->filled('unit_id')) {
                $query->whereHas('pond', fn ($q) => $q->where('unit_id', $request->unit_id));
            }
            return $query;
        }
        if ($user && $user->unit_id) {
            $query->whereHas('pond', fn ($q) => $q->where('unit_id', $user->unit_id));
        } else {
            $query->whereRaw('1 = 0');
        }
        return $query;
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        $foodPrograms = \App\Models\FoodProgram::where('jenis_program', 'perikanan')->whereIn('status', ['aktif', 'perencanaan'])->orderBy('nama_program')->get();
        return view('fishing.create', compact('units', 'foodPrograms'));
    }

    public function store(StoreFishPondRequest $request)
    {
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        FishPond::create($data);
        return redirect()->route('fish-ponds.index')->with('success', 'Kolam berhasil dibuat');
    }

    public function show(FishPond $fishPond)
    {
        $this->authorizeUnitModel($fishPond);
        $fishPond->load(['unit', 'foodProgram', 'participants.wbp', 'fishStocks', 'waterQualityRecords' => fn($q) => $q->latest()->limit(10), 'statusHistories.changedBy']);
        // Dropdown WBP pemeriksa harus ber-scope unit kolam (hindari bocor lintas unit)
        $wbpsQuery = \App\Models\Wbp::where('status', 'aktif');
        if ($fishPond->unit_id) $wbpsQuery->where('unit_id', $fishPond->unit_id);
        $wbps = $wbpsQuery->orderBy('nama_lengkap')->get();
        return view('fishing.show', compact('fishPond', 'wbps'));
    }

    public function edit(FishPond $fishPond)
    {
        $this->authorizeUnitModel($fishPond);
        $units = $this->allowedUnitsQuery()->get();
        $foodPrograms = \App\Models\FoodProgram::where('jenis_program', 'perikanan')->whereIn('status', ['aktif', 'perencanaan'])->orderBy('nama_program')->get();
        return view('fishing.edit', compact('fishPond', 'units', 'foodPrograms'));
    }

    public function update(UpdateFishPondRequest $request, FishPond $fishPond)
    {
        $this->authorizeUnitModel($fishPond);
        $fishPond->update($this->enforceUnitId($request->validated()));
        return redirect()->route('fish-ponds.index')->with('success', 'Kolam berhasil diperbarui');
    }

    public function destroy(FishPond $fishPond)
    {
        $this->authorizeUnitModel($fishPond);
        $fishPond->delete();
        return redirect()->route('fish-ponds.index')->with('success', 'Kolam berhasil dihapus');
    }

    // === WATER QUALITY ===
    public function waterQuality(FishPond $fishPond)
    {
        $this->authorizeUnitModel($fishPond);
        $fishPond->load(['unit', 'fishStocks', 'waterQualityRecords' => fn($q) => $q->latest()->paginate(20)]);
        // Dropdown WBP pemeriksa harus ber-scope unit kolam (hindari bocor lintas unit)
        $wbpsQuery = \App\Models\Wbp::where('status', 'aktif');
        if ($fishPond->unit_id) $wbpsQuery->where('unit_id', $fishPond->unit_id);
        $wbps = $wbpsQuery->orderBy('nama_lengkap')->get();
        return view('fishing.water-quality', compact('fishPond', 'wbps'));
    }

    public function storeWaterQuality(Request $request, FishPond $fishPond)
    {
        $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'jam_pemeriksaan' => 'nullable',
            'suhu_celsius' => 'nullable|numeric|min:0|max:50',
            'ph' => 'nullable|numeric|min:0|max:14',
            'dissolved_oxygen_mgl' => 'nullable|numeric|min:0',
            'amonia_mgl' => 'nullable|numeric|min:0',
            'nitrit_mgl' => 'nullable|numeric|min:0',
            'nitrat_mgl' => 'nullable|numeric|min:0',
            'kekeruhan_ntu' => 'nullable|numeric|min:0',
            'salinitas_ppt' => 'nullable|numeric|min:0',
            'kondisi_air' => 'required|in:baik,kurang_baik,buruk,sangat_buruk',
            'tindakan_perlukan' => 'nullable|string|max:500',
        ]);

        WaterQualityRecord::create(array_merge(
            $request->only([
                'tanggal_pemeriksaan', 'jam_pemeriksaan', 'suhu_celsius', 'ph',
                'dissolved_oxygen_mgl', 'amonia_mgl', 'nitrit_mgl', 'nitrat_mgl',
                'kekeruhan_ntu', 'salinitas_ppt', 'kondisi_air', 'tindakan_perlukan',
            ]),
            [
                'pond_id' => $fishPond->id,
                'pemeriksa_id' => $request->input('pemeriksa_id'),
            ]
        ));

        return back()->with('success', 'Pemeriksaan kualitas air berhasil disimpan!');
    }

    // === FISH STOCK ===
    public function storeFishStock(Request $request, FishPond $fishPond)
    {
        $request->validate([
            'jenis_ikan' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'ukuran_rata_cm' => 'nullable|numeric|min:0',
            'berat_rata_gram' => 'nullable|numeric|min:0',
            'tanggal_tebur' => 'required|date',
        ]);

        FishStock::create(array_merge(
            $request->only([
                'jenis_ikan', 'kategori', 'jumlah', 'ukuran_rata_cm',
                'berat_rata_gram', 'tanggal_tebur', 'harga_benih', 'keterangan',
            ]),
            [
                'kode_stok_ikan' => 'STK-' . strtoupper(uniqid()),
                'pond_id' => $fishPond->id,
                'unit_id' => auth()->user()->unit_id,
                'status' => 'hidup',
            ]
        ));

        return back()->with('success', 'Stok ikan berhasil ditambahkan!');
    }

    /**
     * Ubah status kolam ikan (aksi cepat dari halaman detail).
     */
    public function updateStatus(Request $request, FishPond $fishPond)
    {
        $this->authorizeUnitModel($fishPond);
        $request->validate(['status' => 'required|in:aktif,kering,renovasi,nonaktif']);
        $fishPond->update(['status' => $request->status]);
        return back()->with('success', 'Status kolam diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

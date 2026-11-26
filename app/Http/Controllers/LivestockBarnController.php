<?php

namespace App\Http\Controllers;

use App\Models\LivestockBarn;
use App\Models\LivestockHealthRecord;
use App\Models\LivestockInventory;
use App\Models\Unit;
use App\Models\Wbp;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreLivestockBarnRequest;
use App\Http\Requests\Web\UpdateLivestockBarnRequest;

class LivestockBarnController extends Controller
{
    use UnitScope;
    public function index(Request $request)
    {
        $query = LivestockBarn::with('unit');
        $this->applyUnitScope($query, $request);
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kandang', 'LIKE', "%{$search}%")->orWhere('kode_kandang', 'LIKE', "%{$search}%");
            });
        }
        $barns = $query->orderBy('kode_kandang')->paginate(15)->withQueryString();

        // Statistik (ber-scope unit) — dihitung di controller agar tidak query di view & tidak bocor lintas unit
        $kandangAktif = LivestockBarn::where('status', 'aktif');
        $this->applyUnitScope($kandangAktif, $request);
        $ternakHidup = LivestockInventory::where('status', 'hidup');
        $this->applyUnitScope($ternakHidup, $request);
        $ternakPanen = LivestockInventory::where('status', 'dipanen');
        $this->applyUnitScope($ternakPanen, $request);
        $periksaMingguIni = LivestockHealthRecord::where('tanggal_pemeriksaan', '>=', now()->subWeek());
        $user = auth()->user();
        if ($user && $user->hasRole('super-admin')) {
            if ($request->filled('unit_id')) $periksaMingguIni->whereHas('barn', fn ($q) => $q->where('unit_id', $request->unit_id));
        } elseif ($user && $user->unit_id) {
            $periksaMingguIni->whereHas('barn', fn ($q) => $q->where('unit_id', $user->unit_id));
        } else {
            $periksaMingguIni->whereRaw('1 = 0');
        }

        $stats = [
            'kandang_aktif' => $kandangAktif->count(),
            'ternak_hidup' => (float) $ternakHidup->sum('jumlah'),
            'ternak_panen' => (float) $ternakPanen->sum('jumlah'),
            'periksa_minggu_ini' => $periksaMingguIni->count(),
        ];
        return view('livestock.index', compact('barns', 'stats'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        $foodPrograms = \App\Models\FoodProgram::where('jenis_program', 'peternakan')->whereIn('status', ['aktif', 'perencanaan'])->orderBy('nama_program')->get();
        return view('livestock.create', compact('units', 'foodPrograms'));
    }

    public function store(StoreLivestockBarnRequest $request)
    {
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        $data['status'] = 'aktif';
        LivestockBarn::create($data);
        return redirect()->route('livestock-barns.index')->with('success', 'Kandang berhasil dibuat');
    }

    public function show(LivestockBarn $livestockBarn)
    {
        $this->authorizeUnitModel($livestockBarn);
        $livestockBarn->load(['unit', 'foodProgram', 'participants.wbp', 'inventories.livestockType', 'healthRecords', 'statusHistories.changedBy']);
        // Dropdown WBP PIC harus ber-scope unit kandang (hindari bocor lintas unit)
        $wbpsQuery = \App\Models\Wbp::where('status', 'aktif');
        if ($livestockBarn->unit_id) $wbpsQuery->where('unit_id', $livestockBarn->unit_id);
        $wbps = $wbpsQuery->orderBy('nama_lengkap')->get();
        $livestockTypes = \App\Models\LivestockType::orderBy('nama_jenis')->get();
        return view('livestock.show', compact('livestockBarn', 'wbps', 'livestockTypes'));
    }

    public function edit(LivestockBarn $livestockBarn)
    {
        $this->authorizeUnitModel($livestockBarn);
        $units = $this->allowedUnitsQuery()->get();
        $foodPrograms = \App\Models\FoodProgram::where('jenis_program', 'peternakan')->whereIn('status', ['aktif', 'perencanaan'])->orderBy('nama_program')->get();
        return view('livestock.edit', compact('livestockBarn', 'units', 'foodPrograms'));
    }

    public function update(UpdateLivestockBarnRequest $request, LivestockBarn $livestockBarn)
    {
        $this->authorizeUnitModel($livestockBarn);
        $livestockBarn->update($this->enforceUnitId($request->validated()));
        return redirect()->route('livestock-barns.index')->with('success', 'Kandang berhasil diperbarui');
    }

    public function destroy(LivestockBarn $livestockBarn)
    {
        $this->authorizeUnitModel($livestockBarn);
        $livestockBarn->delete();
        return redirect()->route('livestock-barns.index')->with('success', 'Kandang berhasil dihapus');
    }

    // === HEALTH RECORDS ===
    public function storeHealthRecord(Request $request, LivestockBarn $livestockBarn)
    {
        $request->validate([
            'jenis_pemeriksaan' => 'required|in:vaksinasi,pengobatan,inspeksi,obat_cacing,vitamin',
            'tanggal_pemeriksaan' => 'required|date',
            'wbp_pic_id' => 'nullable|exists:wbp,id',
            'hasil' => 'required|in:sehat,sakit,perlu_perawatan,meninggal',
            'diagnosa' => 'nullable|string|max:500',
            'tindakan' => 'nullable|string|max:500',
        ]);

        LivestockHealthRecord::create(array_merge(
            $request->only([
                'jenis_pemeriksaan', 'tanggal_pemeriksaan', 'biaya',
                'wbp_pic_id', 'hasil', 'diagnosa', 'tindakan', 'keterangan',
            ]),
            [
                'kode_kesehatan' => 'KSH-' . strtoupper(uniqid()),
                'barn_id' => $livestockBarn->id,
            ]
        ));

        return back()->with('success', 'Rekam medis berhasil disimpan!');
    }

    // === LIVESTOCK INVENTORY ===
    public function storeInventory(Request $request, LivestockBarn $livestockBarn)
    {
        $request->validate([
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
        ]);

        LivestockInventory::create(array_merge(
            $request->only([
                'livestock_type_id', 'jumlah', 'berat_rata_kg', 'tanggal_masuk',
                'asal', 'harga_pokok', 'keterangan',
            ]),
            [
                'kode_ternak' => 'TRK-' . strtoupper(uniqid()),
                'barn_id' => $livestockBarn->id,
                'unit_id' => auth()->user()->unit_id,
                'status' => 'hidup',
            ]
        ));

        return back()->with('success', 'Ternak berhasil ditambahkan!');
    }

    /**
     * Ubah status kandang (aksi cepat dari halaman detail).
     */
    public function updateStatus(Request $request, LivestockBarn $livestockBarn)
    {
        $this->authorizeUnitModel($livestockBarn);
        $request->validate(['status' => 'required|in:aktif,renovasi,nonaktif']);
        $livestockBarn->update(['status' => $request->status]);
        return back()->with('success', 'Status kandang diperbarui menjadi ' . ucfirst($request->status) . '.');
    }
}

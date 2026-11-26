<?php

namespace App\Http\Controllers;

use App\Models\Wbp;
use App\Models\WbpDocument;
use App\Models\WbpFamilyMember;
use App\Models\Unit;
use App\Models\Block;
use App\Models\Room;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Web\StoreWbpRequest;
use App\Http\Requests\Web\UpdateWbpRequest;

class WbpController extends Controller
{
    use UnitScope;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->canInput()) {
                abort(403, 'Anda tidak memiliki akses ke modul ini.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy', 'storeDocument', 'updateDocument', 'destroyDocument', 'storeFamilyMember', 'updateFamilyMember', 'destroyFamilyMember', 'updateStatus']);
    }

    public function index(Request $request)
    {
        $query = Wbp::with(['unit', 'block', 'room'])->withCount('statusHistories');
        $this->applyUnitScope($query, $request);
        if ($search = $request->input('search')) {
            $query->search($search);
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->from_date) $query->where('tanggal_masuk', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_masuk', '<=', $request->to_date);
        $wbp = $query->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        // Statistik (ber-scope unit) — dihitung di controller agar tidak query di view & tidak bocor lintas unit
        $statAktif = Wbp::where('status', 'aktif');
        $this->applyUnitScope($statAktif, $request);
        $statBaru = Wbp::where('tanggal_masuk', '>=', now()->subMonth());
        $this->applyUnitScope($statBaru, $request);
        $statKeluar = Wbp::where('status', 'dikeluarkan');
        $this->applyUnitScope($statKeluar, $request);

        $stats = [
            'aktif' => $statAktif->count(),
            'baru' => $statBaru->count(),
            'keluar' => $statKeluar->count(),
        ];
        return view('wbp.index', compact('wbp', 'stats'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        $blocks = Block::orderBy('kode_blok')->get();
        $rooms = Room::orderBy('kode_kamar')->get();
        return view('wbp.create', compact('units', 'blocks', 'rooms'));
    }

    public function store(StoreWbpRequest $request)
    {
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        $data['masa_hukuman_selesai'] = \Carbon\Carbon::parse($data['tanggal_masuk'])->addDays($data['masa_hukuman_hari']);
        $data['status'] = 'aktif';
        Wbp::create($data);
        return redirect()->route('wbp.index')->with('success', 'WBP berhasil didaftarkan');
    }

    public function show(Wbp $wbp)
    {
        $this->authorizeUnitModel($wbp);
        $wbp->load(['unit', 'block', 'room', 'documents', 'familyMembers', 'statusHistories.changedBy']);
        return view('wbp.show', compact('wbp'));
    }

    public function edit(Wbp $wbp)
    {
        $this->authorizeUnitModel($wbp);
        $units = $this->allowedUnitsQuery()->get();
        $blocks = Block::orderBy('kode_blok')->get();
        $rooms = Room::orderBy('kode_kamar')->get();
        return view('wbp.edit', compact('wbp', 'units', 'blocks', 'rooms'));
    }

    public function update(UpdateWbpRequest $request, Wbp $wbp)
    {
        $this->authorizeUnitModel($wbp);
        $data = $request->validated();
        $data = $this->enforceUnitId($data);
        $wbp->update($data);
        return redirect()->route('wbp.index')->with('success', 'Data WBP berhasil diperbarui');
    }

    public function destroy(Wbp $wbp)
    {
        $this->authorizeUnitModel($wbp);
        $wbp->delete();
        return redirect()->route('wbp.index')->with('success', 'Data WBP berhasil dihapus');
    }

    // === DOKUMEN ===
    public function storeDocument(Request $request, Wbp $wbp)
    {
        $this->authorizeUnitModel($wbp);
        $data = $request->validate([
            'jenis_dokumen' => 'required|string|max:50',
            'nomor_dokumen' => 'nullable|string|max:100',
            'nama_dokumen' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tanggal_dokumen' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('documents/' . $wbp->id, 'public');
        }
        $data['wbp_id'] = $wbp->id;
        WbpDocument::create($data);

        return back()->with('success', 'Dokumen berhasil ditambahkan');
    }

    public function updateDocument(Request $request, Wbp $wbp, WbpDocument $document)
    {
        $this->authorizeUnitModel($wbp);
        $data = $request->validate([
            'jenis_dokumen' => 'required|string|max:50',
            'nomor_dokumen' => 'nullable|string|max:100',
            'nama_dokumen' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tanggal_dokumen' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('file')) {
            // Ganti file lama (jika ada) dengan file baru
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $data['file_path'] = $request->file('file')->store('documents/' . $wbp->id, 'public');
        }
        $document->update($data);

        return back()->with('success', 'Dokumen berhasil diperbarui');
    }

    public function destroyDocument(Wbp $wbp, WbpDocument $document)
    {
        $this->authorizeUnitModel($wbp);
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return back()->with('success', 'Dokumen berhasil dihapus');
    }

    // === KELUARGA ===

    /**
     * Normalisasi input keluarga: trim NIK, bersihkan spasi/dash pada HP, kosongkan string kosong.
     */
    private function normalizeFamilyInput(Request $request): void
    {
        $request->merge([
            'nik' => trim((string) $request->nik) ?: null,
            'nomor_hp' => preg_replace('/[\s\-]/', '', (string) $request->nomor_hp) ?: null,
        ]);
    }

    public function storeFamilyMember(Request $request, Wbp $wbp)
    {
        $this->authorizeUnitModel($wbp);
        $this->normalizeFamilyInput($request);
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'hubungan' => ['required', 'in:suami,istri,ayah,ibu,anak,saudara,lainnya'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:wbp_family_members,nik'],
            'nomor_hp' => ['nullable', 'string', 'max:20', 'regex:/^(08[0-9]{8,12}|\+62[0-9]{8,13}|62[0-9]{8,13})$/'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'is_wali' => ['nullable', 'boolean'],
        ], [
            'nik.unique' => 'NIK :input sudah terdaftar pada anggota keluarga lain.',
            'nomor_hp.regex' => 'Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx atau +628xxxxxxxxxx.',
        ]);

        $data['wbp_id'] = $wbp->id;
        $data['is_wali'] = $request->boolean('is_wali');
        WbpFamilyMember::create($data);

        return back()->with('success', 'Anggota keluarga berhasil ditambahkan');
    }

    public function updateFamilyMember(Request $request, Wbp $wbp, WbpFamilyMember $familyMember)
    {
        $this->authorizeUnitModel($wbp);
        $this->normalizeFamilyInput($request);
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'hubungan' => ['required', 'in:suami,istri,ayah,ibu,anak,saudara,lainnya'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:wbp_family_members,nik,' . $familyMember->id],
            'nomor_hp' => ['nullable', 'string', 'max:20', 'regex:/^(08[0-9]{8,12}|\+62[0-9]{8,13}|62[0-9]{8,13})$/'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'is_wali' => ['nullable', 'boolean'],
        ], [
            'nik.unique' => 'NIK :input sudah terdaftar pada anggota keluarga lain.',
            'nomor_hp.regex' => 'Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx atau +628xxxxxxxxxx.',
        ]);

        $data['is_wali'] = $request->boolean('is_wali');
        $familyMember->update($data);

        return back()->with('success', 'Anggota keluarga berhasil diperbarui');
    }

    public function destroyFamilyMember(Wbp $wbp, WbpFamilyMember $familyMember)
    {
        $this->authorizeUnitModel($wbp);
        $familyMember->delete();
        return back()->with('success', 'Anggota keluarga berhasil dihapus');
    }

    /**
     * Ubah status WBP (aksi cepat dari halaman detail).
     */
    public function updateStatus(Request $request, Wbp $wbp)
    {
        $this->authorizeUnitModel($wbp);
        $request->validate([
            'status' => 'required|in:aktif,dikeluarkan,pindah,meninggal,bebas',
        ]);

        $wbp->update(['status' => $request->status]);
        return back()->with('success', 'Status WBP diperbarui menjadi ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }
}

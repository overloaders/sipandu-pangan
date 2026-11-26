<?php

namespace App\Http\Controllers;

use App\Models\TrainingProgram;
use App\Models\TrainingParticipant;
use App\Models\TrainingCertification;
use App\Models\Wbp;
use App\Models\Unit;
use App\Traits\UnitScope;
use App\Http\Requests\Web\StoreTrainingProgramRequest;
use App\Http\Requests\Web\UpdateTrainingProgramRequest;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    use UnitScope;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->hasRole(['super-admin', 'admin', 'supervisor'])) {
                abort(403, 'Anda tidak memiliki akses ke modul ini.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = TrainingProgram::with(['unit', 'participants.wbp']);
        $this->applyUnitScope($query, $request);

        if ($request->status) $query->where('status', $request->status);
        if ($request->tipe) $query->where('tipe', $request->tipe);
        if ($request->from_date) $query->where('tanggal_mulai', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_mulai', '<=', $request->to_date);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_pelatihan', 'like', "%{$request->search}%")
                  ->orWhere('nama_pelatihan', 'like', "%{$request->search}%");
            });
        }

        $programs = $query->orderByDesc('tanggal_mulai')->paginate(15);
        return view('training.index', compact('programs'));
    }

    public function create()
    {
        $units = $this->allowedUnitsQuery()->get();
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('training.create', compact('units', 'wbps'));
    }

    public function store(StoreTrainingProgramRequest $request)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $request->validate([
            'kode_pelatihan' => 'required|unique:training_programs,kode_pelatihan',
            'nama_pelatihan' => 'required',
            'kategori' => 'required',
            'unit_id' => 'required|exists:units,id',
            'tanggal_mulai' => 'required|date',
            'status' => 'required',
        ]);

        $program = TrainingProgram::create($request->only([
            'kode_pelatihan', 'nama_pelatihan', 'deskripsi', 'kategori', 'unit_id',
            'instructor_user_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_pelatihan',
            'kapasitas_peserta', 'biaya', 'lokasi', 'tipe', 'status',
            'sertifikat_diberikan', 'materi_pokok',
        ]));

        return redirect()->route('training.index')->with('success', 'Program pelatihan berhasil ditambahkan!');
    }

    public function show(TrainingProgram $training)
    {
        $this->authorizeUnitModel($training);
        $training->load(['unit', 'participants.wbp', 'certifications.wbp', 'statusHistories.changedBy']);
        // Load certifications for this program and attach to participants
        $certsByWbp = TrainingCertification::where('training_program_id', $training->id)
            ->whereIn('wbp_id', $training->participants->pluck('wbp_id'))
            ->get()->keyBy('wbp_id');
        foreach ($training->participants as $p) {
            $p->setRelation('certification', $certsByWbp->get($p->wbp_id));
        }
        return view('training.show', ['program' => $training]);
    }

    public function edit(TrainingProgram $training)
    {
        $this->authorizeUnitModel($training);
        if (in_array($training->status, ['selesai', 'dibatalkan'])) {
            return redirect()->route('training.show', $training->id)
                ->with('error', 'Program dengan status "' . ucfirst($training->status) . '" tidak dapat diedit.');
        }
        $units = $this->allowedUnitsQuery()->get();
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('training.edit', ['program' => $training, 'units' => $units, 'wbps' => $wbps]);
    }

    public function update(UpdateTrainingProgramRequest $request, TrainingProgram $training)
    {
        $request->merge(['unit_id' => $this->resolveUnitId($request->unit_id)]);
        $this->authorizeUnitModel($training);
        if (in_array($training->status, ['selesai', 'dibatalkan'])) {
            return redirect()->route('training.show', $training->id)
                ->with('error', 'Program dengan status "' . ucfirst($training->status) . '" tidak dapat diupdate.');
        }

        $request->validate([
            'nama_pelatihan' => 'required',
            'kategori' => 'required',
            'unit_id' => 'required|exists:units,id',
            'tanggal_mulai' => 'required|date',
            'status' => 'required',
        ]);

        $training->update($request->only([
            'nama_pelatihan', 'deskripsi', 'kategori', 'unit_id',
            'instructor_user_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_pelatihan',
            'kapasitas_peserta', 'biaya', 'lokasi', 'tipe', 'status',
            'sertifikat_diberikan', 'materi_pokok',
        ]));

        return redirect()->route('training.index')->with('success', 'Program pelatihan berhasil diupdate!');
    }

    public function destroy(TrainingProgram $training)
    {
        $this->authorizeUnitModel($training);
        $training->delete();
        return redirect()->route('training.index')->with('success', 'Program pelatihan berhasil dihapus!');
    }

    public function participants(TrainingProgram $program)
    {
        $program->load(['participants.wbp', 'participants.certification', 'unit']);
        // Load certifications for this program and attach to participants
        $certsByWbp = TrainingCertification::where('training_program_id', $program->id)
            ->whereIn('wbp_id', $program->participants->pluck('wbp_id'))
            ->get()->keyBy('wbp_id');
        foreach ($program->participants as $p) {
            $p->setRelation('certification', $certsByWbp->get($p->wbp_id));
        }
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('training.participants', ['program' => $program, 'wbps' => $wbps]);
    }

    public function storeParticipant(Request $request, TrainingProgram $program)
    {
        $request->validate([
            'wbp_id' => 'required|exists:wbp,id',
        ]);

        $program->participants()->create([
            'wbp_id' => $request->wbp_id,
            'tanggal_daftar' => now(),
            'status' => 'terdaftar',
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function destroyParticipant(TrainingProgram $program, TrainingParticipant $participant)
    {
        $participant->delete();
        return back()->with('success', 'Peserta berhasil dihapus!');
    }

    public function updateStatus($program, Request $request)
    {
        $training = TrainingProgram::findOrFail($program);
        $this->authorizeUnitModel($training);

        if (in_array($training->status, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Program dengan status "' . ucfirst($training->status) . '" tidak dapat diubah.');
        }

        $request->validate([
            'status' => 'required|in:perencanaan,terbuka,berlangsung,selesai,dibatalkan',
        ]);

        $newStatus = $request->input('status');
        $training->update(['status' => $newStatus]);

        return back()->with('success', 'Status berhasil diubah ke ' . ucfirst($newStatus) . '!');
    }

    public function updateNilai(TrainingProgram $program, TrainingParticipant $participant)
    {
        $request = request();
        $request->validate([
            'nilai_akhir' => 'required|numeric|min:0|max:100',
        ]);

        $participant->update([
            'nilai_akhir' => $request->nilai_akhir,
        ]);

        return back()->with('success', 'Nilai ' . ($participant->wbp->nama_lengkap ?? '') . ' berhasil disimpan!');
    }

    public function issueParticipantCert(TrainingProgram $program, TrainingParticipant $participant)
    {
        // Check if certification already exists
        $existing = TrainingCertification::where('training_program_id', $program->id)
            ->where('wbp_id', $participant->wbp_id)
            ->first();

        if ($existing) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Sertifikat sudah diterbitkan!'], 422);
            }
            return back()->with('warning', 'Sertifikat sudah diterbitkan!');
        }

        $namaSertifikat = 'Sertifikat ' . $program->kategori . ' - ' . $program->nama_pelatihan;

        TrainingCertification::create([
            'training_program_id' => $program->id,
            'wbp_id' => $participant->wbp_id,
            'kode_sertifikat' => 'SC-' . $program->kode_pelatihan . '-' . strtoupper(substr(uniqid(), -6)),
            'nama_sertifikat' => $namaSertifikat,
            'nomor_sertifikat_resmi' => 'NOMOR-' . strtoupper($program->kode_pelatihan) . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
            'tanggal_terbit' => now(),
            'tingkat' => 'dasar',
            'status' => 'aktif',
        ]);

        // Update participant status
        $participant->update(['status' => 'selesai']);

        $nama = $participant->wbp->nama_lengkap ?? 'WBP';
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Sertifikat berhasil diterbitkan untuk ' . $nama . '!']);
        }
        return back()->with('success', 'Sertifikat berhasil diterbitkan untuk ' . $nama . '!');
    }

    public function printCertificate(TrainingProgram $program, TrainingCertification $certificate)
    {
        $certificate->load(['wbp', 'trainingProgram.unit']);
        return view('training.certificate', [
            'program' => $program,
            'certificate' => $certificate,
            'wbp' => $certificate->wbp,
        ]);
    }

    public function certifications(TrainingProgram $program)
    {
        $program->load(['certifications.wbp', 'participants.wbp', 'unit']);
        $wbps = Wbp::where('status', 'aktif')->orderBy('nama_lengkap')->get();
        return view('training.certifications', ['program' => $program, 'wbps' => $wbps]);
    }

    public function storeCertification(Request $request, TrainingProgram $program)
    {
        $request->validate([
            'wbp_id' => 'required|exists:wbp,id',
            'nama_sertifikat' => 'required',
        ]);

        $nomorResmi = $request->nomor_sertifikat_resmi ?: ('NOMOR-' . strtoupper($program->kode_pelatihan) . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)));

        TrainingCertification::create([
            'training_program_id' => $program->id,
            'wbp_id' => $request->wbp_id,
            'kode_sertifikat' => 'SC-' . $program->kode_pelatihan . '-' . strtoupper(substr(uniqid(), -6)),
            'nama_sertifikat' => $request->nama_sertifikat,
            'nomor_sertifikat_resmi' => $nomorResmi,
            'tanggal_terbit' => $request->tanggal_terbit ?? now(),
            'tingkat' => $request->tingkat ?? 'dasar',
            'keterangan' => $request->keterangan,
            'status' => 'aktif',
        ]);

        return back()->with('success', 'Sertifikat berhasil diterbitkan!');
    }
}

@extends('adminlte::page')

@section('title', 'Detail Program Pelatihan: ' . ($program->nama_pelatihan ?? ''))

@section('header_actions')
    <a href="{{ route('reports.training-detail', array_merge(['program' => $program->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-success btn-sm" title="Export PDF"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    <a href="{{ route('training.participants', $program->id) }}" class="btn btn-info btn-sm"><i class="fas fa-users mr-1"></i> Peserta</a>
    <a href="{{ route('training.certifications', $program->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-certificate mr-1"></i> Sertifikat</a>
    <a href="{{ route('training.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-graduation-cap mr-2"></i>{{ $program->nama_pelatihan }}</h5>
        <div>
            @if(!in_array($program->status, ['selesai', 'dibatalkan']))
            <a href="{{ route('training.edit', $program->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Kode Pelatihan</th><td>{{ $program->kode_pelatihan }}</td></tr>
                    <tr><th>Nama Pelatihan</th><td>{{ $program->nama_pelatihan }}</td></tr>
                    <tr><th>Kategori</th><td><span class="badge badge-info">{{ ucfirst($program->kategori) }}</span></td></tr>
                    <tr><th>Tipe</th><td>{{ ucfirst($program->tipe ?? '-') }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $program->lokasi ?? '-' }}</td></tr>
                    <tr><th>Unit</th><td>{{ $program->unit->nama_unit ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Tanggal Mulai</th><td>{{ $program->tanggal_mulai ? \Carbon\Carbon::parse($program->tanggal_mulai)->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Tanggal Selesai</th><td>{{ $program->tanggal_selesai ? \Carbon\Carbon::parse($program->tanggal_selesai)->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Jam Pelatihan</th><td>{{ $program->jam_pelatihan ?? '-' }} jam</td></tr>
                    <tr><th>Kapasitas</th><td>{{ $program->kapasitas_peserta ?? '-' }}</td></tr>
                    <tr><th>Biaya</th><td>Rp {{ number_format($program->biaya ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Sertifikat</th><td>{{ $program->sertifikat_diberikan ? 'Ya' : 'Tidak' }}</td></tr>
                    <tr><th>Status</th>
                        <td>{{ ucfirst($program->status) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mt-3">
            @if(in_array($program->status, ['selesai', 'dibatalkan']))
                <div class="alert alert-secondary py-2 px-3 mb-0" style="border-radius:8px;">
                    <i class="fas fa-lock"></i> <strong>Status Terkunci</strong> — Program dengan status "{{ ucfirst($program->status) }}" tidak dapat diubah.
                </div>
            @else
                <form action="{{ route('training.update-status', $program->id) }}" method="POST">
                    @csrf
                    <div class="form-inline">
                        <label class="mr-2"><strong>Ubah Status:</strong></label>
                        <select name="status" class="form-control form-control-sm mr-2">
                            @foreach(['perencanaan', 'berlangsung', 'selesai', 'dibatalkan'] as $s)
                            <option value="{{ $s }}" {{ $program->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </form>
            @endif
        </div>
        @if($program->deskripsi)
        <div class="callout callout-info">
            <h5>Deskripsi</h5>
            <p>{{ $program->deskripsi }}</p>
        </div>
        @endif
        @if($program->materi_pokok)
        <div class="callout callout-warning">
            <h5>Materi Pokok</h5>
            <p>{{ $program->materi_pokok }}</p>
        </div>
        @endif
    </div>
</div>

@if($program->participants->count())
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Peserta ({{ $program->participants->count() }})</h5>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama WBP</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th>Nilai Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($program->participants as $p)
                @php
                    $hasCert = $p->certification !== null;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->wbp->nama_lengkap ?? '-' }}</td>
                    <td>{{ $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($p->status == 'lulus')
                            <span class="badge badge-success">Lulus</span>
                        @elseif($p->status == 'gagal')
                            <span class="badge badge-danger">Gagal</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($p->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $p->nilai_akhir ?? '-' }}</td>
                    <td class="text-nowrap">
                        @if($hasCert)
                            <a href="{{ route('training.print-certificate', [$program->id, $p->certification->id]) }}" class="btn btn-info btn-xs" target="_blank" title="Cetak Sertifikat">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                        @elseif($program->status == 'selesai')
                            <button type="button" class="btn btn-success btn-xs" title="Terbitkan Sertifikat" onclick="swalConfirmPost('{{ route('training.issue-participant-cert', [$program->id, $p->id]) }}', 'Terbitkan Sertifikat?', 'Sertifikat akan diterbitkan untuk {{ addslashes($p->wbp->nama_lengkap ?? '-') }}', '#10b981', 'Terbitkan')">
                                <i class="fas fa-certificate"></i> Terbitkan
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.training-detail', $program) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['perencanaan' => 'warning', 'terbuka' => 'info', 'berlangsung' => 'primary', 'selesai' => 'dark', 'dibatalkan' => 'danger']; @endphp
        <table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($program->statusHistories->sortByDesc('created_at') as $h)
                <tr>
                    <td style="font-size:12px;white-space:nowrap;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($h->status_lama)
                            <span class="badge badge-{{ $sc[$h->status_lama] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_lama)) }}</span>
                            <i class="fas fa-arrow-right mx-1" style="font-size:10px;color:#94a3b8;"></i>
                        @endif
                        <span class="badge badge-{{ $sc[$h->status_baru] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_baru)) }}</span>
                    </td>
                    <td style="font-size:12px;">{{ $h->changedBy?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada perubahan status.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection



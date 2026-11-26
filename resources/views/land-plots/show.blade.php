@extends('adminlte::page')

@section('title', 'Detail Lahan: ' . ($landPlot->nama_lahan ?? ''))

@section('header_actions')
    <a href="{{ route('reports.land-plots-detail', array_merge([$landPlot->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    <a href="{{ route('land-plots.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-map mr-2"></i>{{ $landPlot->nama_lahan }}</h5>
        <div>
            <a href="{{ route('land-plots.edit', $landPlot->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Kode Lahan</th><td>{{ $landPlot->kode_lahan }}</td></tr>
                    <tr><th>Nama Lahan</th><td>{{ $landPlot->nama_lahan }}</td></tr>
                    <tr><th>Unit</th><td>{{ $landPlot->unit->nama_unit ?? '-' }}</td></tr>
                    <tr><th>Luas</th><td>{{ number_format($landPlot->luas_m2, 0, ',', '.') }} m²</td></tr>
                    <tr><th>Jenis Tanah</th><td><span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $landPlot->jenis_tanah ?? '-')) }}</span></td></tr>
                    <tr><th>Sistem Irigasi</th><td>{{ ucfirst(str_replace('_', ' ', $landPlot->sistem_irigasi ?? '-')) }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Status</th>
                        <td>
                            @if($landPlot->status == 'tersedia')
                                <span class="badge badge-success">Tersedia</span>
                            @elseif(in_array($landPlot->status, ['ditanami', 'istirahat', 'renovasi']))
                                <span class="badge badge-warning">{{ ucfirst($landPlot->status) }}</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst(str_replace('_', ' ', $landPlot->status)) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Latitude</th><td>{{ $landPlot->latitude ?? '-' }}</td></tr>
                    <tr><th>Longitude</th><td>{{ $landPlot->longitude ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
        @if($landPlot->keterangan)
        <div class="callout callout-info">
            <h5>Keterangan</h5>
            <p>{{ $landPlot->keterangan }}</p>
        </div>
        @endif
    </div>
</div>

@if($landPlot->farmingPlans && $landPlot->farmingPlans->count())
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-tractor mr-2"></i>Rencana Pertanian di Lahan Ini ({{ $landPlot->farmingPlans->count() }})</h5>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Rencana</th>
                    <th>Status</th>
                    <th>Tanggal Mulai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($landPlot->farmingPlans as $fp)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $fp->kode_rencana ?? '-' }}</td>
                    <td>{{ $fp->nama_rencana }}</td>
                    <td>
                        @if($fp->status == 'proses_tanam')
                            <span class="badge badge-warning">Proses Tanam</span>
                        @elseif($fp->status == 'tumbuh')
                            <span class="badge badge-success">Tumbuh</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $fp->status)) }}</span>
                        @endif
                    </td>
                    <td>{{ $fp->tanggal_mulai ? \Carbon\Carbon::parse($fp->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- UBAH STATUS --}}
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['tersedia' => 'success', 'ditanami' => 'info', 'istirahat' => 'warning', 'renovasi' => 'secondary', 'tidak_digunakan' => 'dark'] as $st => $color)
                @if($st !== $landPlot->status)
                <form action="{{ route('land-plots.status', $landPlot) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status lahan menjadi {{ ucfirst(str_replace('_',' ',$st)) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst(str_replace('_',' ',$st)) }}</button>
                </form>
                @endif
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.land-plots-detail', $landPlot) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['tersedia' => 'success', 'ditanami' => 'info', 'istirahat' => 'warning', 'renovasi' => 'secondary', 'tidak_digunakan' => 'dark']; @endphp
        <table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($landPlot->statusHistories->sortByDesc('created_at') as $h)
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

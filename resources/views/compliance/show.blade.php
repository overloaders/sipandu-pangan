@extends('adminlte::page')

@section('title', 'Detail Catatan Komplians: ' . ($record->judul ?? ''))

@section('header_actions')
    <a href="{{ route('reports.compliance-detail', array_merge(['record' => $record->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-success btn-sm" title="Export PDF"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    <a href="{{ route('compliance.details', $record->id) }}" class="btn btn-info btn-sm"><i class="fas fa-list mr-1"></i> Detail Pemeriksaan</a>
    <a href="{{ route('compliance.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-certificate mr-2"></i>{{ $record->judul }}</h5>
        <div>
            @if(!in_array($record->status, ['kedaluwarsa', 'dibatalkan']))
            <a href="{{ route('compliance.edit', $record->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Kode Komplians</th><td>{{ $record->kode_komplians }}</td></tr>
                    <tr><th>Judul</th><td>{{ $record->judul }}</td></tr>
                    <tr><th>Tipe Sertifikasi</th><td><span class="badge badge-info">{{ strtoupper($record->tipe) }}</span></td></tr>
                    <tr><th>Unit</th><td>{{ $record->unit->nama_unit ?? '-' }}</td></tr>
                    <tr><th>Tanggal Pemeriksaan</th><td>{{ $record->tanggal_pemeriksaan ? \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Berlaku Sampai</th><td>{{ $record->tanggal_berlaku_sampai ? \Carbon\Carbon::parse($record->tanggal_berlaku_sampai)->format('d/m/Y') : '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Nilai Skor</th><td>{{ $record->nilai_skor ?? '-' }}</td></tr>
                    <tr><th>Tingkat Risiko</th>
                        <td>
                            @if($record->tingkat_risiko == 'tinggi')
                                <span class="badge badge-danger">Tinggi</span>
                            @elseif($record->tingkat_risiko == 'sedang')
                                <span class="badge badge-warning">Sedang</span>
                            @else
                                <span class="badge badge-success">{{ ucfirst($record->tingkat_risiko ?? '-') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Hasil</th><td>{{ $record->hasil ?? '-' }}</td></tr>
                    <tr><th>Inspektur</th><td>{{ $record->inspektor->name ?? '-' }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @if($record->status == 'aktif')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($record->status == 'kedaluwarsa')
                                <span class="badge badge-danger">Kedaluwarsa</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if($record->temuan)
        <div class="callout callout-warning">
            <h5>Temuan</h5>
            <p>{{ $record->temuan }}</p>
        </div>
        @endif
        @if($record->rekomendasi)
        <div class="callout callout-info">
            <h5>Rekomendasi</h5>
            <p>{{ $record->rekomendasi }}</p>
        </div>
        @endif
    </div>
</div>

@if($record->details && $record->details->count())
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;">Detail Pemeriksaan ({{ $record->details->count() }})</h5>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Pemeriksaan</th>
                    <th>Status</th>
                    <th>Bobot</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->details as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->item_pemeriksaan }}</td>
                    <td>
                        @if($d->status_pemeriksaan == 'memenuhi')
                            <span class="badge badge-success">Memenuhi</span>
                        @elseif($d->status_pemeriksaan == 'tidak_memenuhi')
                            <span class="badge badge-danger">Tidak Memenuhi</span>
                        @else
                            <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $d->status_pemeriksaan)) }}</span>
                        @endif
                    </td>
                    <td>{{ $d->bobot ?? '-' }}</td>
                    <td>{{ $d->nilai ?? '-' }}</td>
                    <td>{{ $d->catatan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if(!in_array($record->status, ['kedaluwarsa', 'dibatalkan']))
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['draft' => 'secondary', 'aktif' => 'success', 'perlu_perbaikan' => 'warning', 'kedaluwarsa' => 'danger', 'dibatalkan' => 'dark'] as $st => $color)
                @if($st !== $record->status)
                <form action="{{ route('compliance.status', $record) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst(str_replace('_',' ',$st)) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst(str_replace('_',' ',$st)) }}</button>
                </form>
                @endif
            @endforeach
        </div>
        <small class="form-text text-muted mt-2">Rekaman dengan status <strong>kedaluwarsa / dibatalkan</strong> terkunci dan tidak dapat diubah lagi.</small>
    </div>
</div>
@endif

{{-- RIWAYAT STATUS --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.compliance-detail', $record) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $cmpColors = ['draft' => 'secondary', 'aktif' => 'success', 'perlu_perbaikan' => 'warning', 'kedaluwarsa' => 'danger', 'dibatalkan' => 'dark']; @endphp
        <table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($record->statusHistories->sortByDesc('created_at') as $h)
                <tr>
                    <td style="font-size:12px;white-space:nowrap;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($h->status_lama)
                            <span class="badge badge-{{ $cmpColors[$h->status_lama] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_lama)) }}</span>
                            <i class="fas fa-arrow-right mx-1" style="font-size:10px;color:#94a3b8;"></i>
                        @endif
                        <span class="badge badge-{{ $cmpColors[$h->status_baru] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_baru)) }}</span>
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

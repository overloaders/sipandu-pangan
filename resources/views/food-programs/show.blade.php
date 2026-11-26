@extends('adminlte::master')
@section('title', 'Detail Program: ' . $foodProgram->nama_program)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-seedling mr-2"></i>Detail Program</h5>
                <div>
                    @if(!in_array($foodProgram->status, ['selesai', 'dibatalkan']))
                    <a href="{{ route('food-programs.edit', $foodProgram) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-edit mr-1"></i> Edit</a>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('food-programs.destroy', $foodProgram) }}','{{ $foodProgram->nama_program }}')" title="Hapus"><i class="fas fa-trash"></i></button>
                    <div>
                        <a href="{{ route('reports.food-programs-detail', array_merge([$foodProgram->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                        <a href="{{ route('food-programs.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Program</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $foodProgram->kode_program }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama Program</td><td style="font-weight:700;">{{ $foodProgram->nama_program }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jenis</td><td><span class="badge badge-info">{{ ucfirst($foodProgram->jenis_program) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $foodProgram->status=='aktif'?'success':'warning' }}">{{ ucfirst($foodProgram->status) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Unit</td><td>{{ $foodProgram->unit?->nama_unit ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tanggal Mulai</td><td>{{ $foodProgram->tanggal_mulai?->format('d M Y') ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Anggaran</td><td>Rp {{ number_format($foodProgram->anggaran,0,',','.') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Deskripsi</td><td>{{ $foodProgram->deskripsi ?? '-' }}</td></tr>
                </table></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            @php
                $farmCount = $foodProgram->farmingPlans->sum(fn($fp) => $fp->participants->count());
                $liveCount = $foodProgram->livestockBarns->sum(fn($b) => $b->participants->count());
                $fishCount = $foodProgram->fishPonds->sum(fn($p) => $p->participants->count());
                $totalPeserta = $farmCount + $liveCount + $fishCount;
            @endphp
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Peserta ({{ $totalPeserta }})</h5></div>
            <div class="card-body">
                @forelse($foodProgram->farmingPlans as $fp)
                    @if($fp->participants->count() > 0)
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge badge-success mr-2" style="font-size:10px;">🚜 {{ $fp->kode_rencana }}</span>
                            <span style="font-weight:600;font-size:12px;">{{ $fp->cropType?->nama_tanaman ?? '-' }}</span>
                        </div>
                        @foreach($fp->participants as $p)
                        <div class="d-flex align-items-center mb-1 ml-3 p-1" style="background:#f8fafc;border-radius:6px;font-size:11px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:22px;height:22px;background:#d1fae5;color:#059669;font-size:9px;font-weight:600;">{{ strtoupper(substr($p->wbp->nama_lengkap ?? 'X',0,1)) }}</div>
                            <span>{{ $p->wbp?->nama_lengkap ?? '-' }}</span>
                            <span class="ml-auto" style="color:#94a3b8;">{{ ucfirst($p->peran ?? '-') }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                @empty
                @endforelse
                @forelse($foodProgram->livestockBarns as $barn)
                    @if($barn->participants->count() > 0)
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge badge-warning mr-2" style="font-size:10px;">🐄 {{ $barn->kode_kandang }}</span>
                            <span style="font-weight:600;font-size:12px;">{{ $barn->nama_kandang }}</span>
                        </div>
                        @foreach($barn->participants as $p)
                        <div class="d-flex align-items-center mb-1 ml-3 p-1" style="background:#fff7ed;border-radius:6px;font-size:11px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:22px;height:22px;background:#fef3c7;color:#d97706;font-size:9px;font-weight:600;">{{ strtoupper(substr($p->wbp->nama_lengkap ?? 'X',0,1)) }}</div>
                            <span>{{ $p->wbp?->nama_lengkap ?? '-' }}</span>
                            <span class="ml-auto" style="color:#94a3b8;">{{ ucfirst($p->peran ?? '-') }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                @empty
                @endforelse
                @forelse($foodProgram->fishPonds as $pond)
                    @if($pond->participants->count() > 0)
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge badge-primary mr-2" style="font-size:10px;">🐟 {{ $pond->kode_kolam }}</span>
                            <span style="font-weight:600;font-size:12px;">{{ $pond->nama_kolam }}</span>
                        </div>
                        @foreach($pond->participants as $p)
                        <div class="d-flex align-items-center mb-1 ml-3 p-1" style="background:#eff6ff;border-radius:6px;font-size:11px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:22px;height:22px;background:#dbeafe;color:#2563eb;font-size:9px;font-weight:600;">{{ strtoupper(substr($p->wbp->nama_lengkap ?? 'X',0,1)) }}</div>
                            <span>{{ $p->wbp?->nama_lengkap ?? '-' }}</span>
                            <span class="ml-auto" style="color:#94a3b8;">{{ ucfirst($p->peran ?? '-') }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                @empty
                @endforelse
                @if($totalPeserta == 0)
                <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada peserta di program ini</p>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-tractor mr-2"></i>Rencana Kerja</h5></div>
            <div class="card-body">
                @forelse($foodProgram->farmingPlans->take(5) as $fp)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;font-size:12px;">
                    <div><span class="badge badge-secondary">{{ $fp->kode_rencana }}</span> {{ $fp->cropType?->nama_tanaman ?? '-' }}</div>
                    <span class="badge badge-{{ $fp->status=='panen'?'success':'info' }}">{{ ucfirst(str_replace('_',' ',$fp->status)) }}</span>
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada rencana kerja</p> @endforelse
            </div>
        </div>
        @if($foodProgram->livestockBarns->count() > 0)
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-paw mr-2"></i>Peternakan ({{ $foodProgram->livestockBarns->count() }})</h5></div>
            <div class="card-body">
                @foreach($foodProgram->livestockBarns as $barn)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;font-size:12px;">
                    <div><span class="badge badge-warning">{{ $barn->kode_kandang }}</span> {{ $barn->nama_kandang }} ({{ ucfirst($barn->jenis_ternak) }})</div>
                    <span class="badge badge-{{ $barn->status=='aktif'?'success':'secondary' }}">{{ ucfirst($barn->status) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if($foodProgram->fishPonds->count() > 0)
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-fish mr-2"></i>Perikanan ({{ $foodProgram->fishPonds->count() }})</h5></div>
            <div class="card-body">
                @foreach($foodProgram->fishPonds as $pond)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;font-size:12px;">
                    <div><span class="badge badge-primary">{{ $pond->kode_kolam }}</span> {{ $pond->nama_kolam }} ({{ $pond->tipe_kolam }})</div>
                    <span class="badge badge-{{ $pond->status=='aktif'?'success':'secondary' }}">{{ ucfirst($pond->status) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- UBAH STATUS --}}
@if(!in_array($foodProgram->status, ['selesai', 'dibatalkan']))
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['perencanaan' => 'secondary', 'aktif' => 'success', 'selesai' => 'dark', 'dibatalkan' => 'danger'] as $st => $color)
                @if($st !== $foodProgram->status)
                <form action="{{ route('food-programs.status', $foodProgram) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
        <small class="form-text text-muted mt-2">Program dengan status <strong>selesai / dibatalkan</strong> tidak dapat diubah lagi.</small>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.food-programs-detail', $foodProgram) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['perencanaan' => 'secondary', 'aktif' => 'success', 'selesai' => 'dark', 'dibatalkan' => 'danger']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($foodProgram->statusHistories->sortByDesc('created_at') as $h)
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
        </table></div>
    </div>
</div>
@endsection

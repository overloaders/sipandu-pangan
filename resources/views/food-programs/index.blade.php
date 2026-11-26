@extends('adminlte::master')

@section('title', 'Program Ketahanan Pangan')

@section('header_actions')
<a href="{{ route('reports.food-programs', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-file-pdf mr-1"></i> Export PDF
</a>
<a href="{{ route('food-programs.create') }}" class="btn btn-success btn-sm">
    <i class="fas fa-plus mr-1"></i> Tambah Program
</a>
@endsection

@section('content')
{{-- Summary Cards --}}
<div class="row mb-4 dash-row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-seedling"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['total']) }}</div>
                    <div class="stat-label">Total Program</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-play-circle"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['aktif']) }}</div>
                    <div class="stat-label">Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fef3c7;color:#d97706;width:40px;height:40px;font-size:16px;"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['perencanaan']) }}</div>
                    <div class="stat-label">Perencanaan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #8b5cf6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#ede9fe;color:#7c3aed;width:40px;height:40px;font-size:16px;"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['selesai']) }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Program Type Flow Cards --}}
<div class="row mb-4 dash-row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-sitemap mr-2"></i>Jenis Program</h5>
            </div>
            <div class="card-body">
                @php
                    $jenisCounts = $stats['jenis'];
                    $jenisList = [
                        'pertanian'  => ['icon' => 'fa-tractor',      'color' => 'linear-gradient(135deg,#10b981,#059669)', 'label' => 'Pertanian'],
                        'peternakan' => ['icon' => 'fa-paw',          'color' => 'linear-gradient(135deg,#f59e0b,#d97706)', 'label' => 'Peternakan'],
                        'perikanan'  => ['icon' => 'fa-fish',         'color' => 'linear-gradient(135deg,#3b82f6,#2563eb)', 'label' => 'Perikanan'],
                        'integrated' => ['icon' => 'fa-project-diagram', 'color' => 'linear-gradient(135deg,#8b5cf6,#7c3aed)', 'label' => 'Integrated'],
                        'pengolahan' => ['icon' => 'fa-blender',      'color' => 'linear-gradient(135deg,#ec4899,#db2777)', 'label' => 'Pengolahan'],
                    ];
                @endphp
                <div class="row">
                    @foreach($jenisList as $jenis => $cfg)
                    <div class="col">
                        <a href="{{ route('food-programs.index', array_merge(request()->query(), ['jenis_program' => $jenis])) }}"
                           class="flow-card flow-card-link {{ request('jenis_program') == $jenis ? 'active' : '' }}"
                           style="background:{{ $cfg['color'] }};"
                           title="Lihat program {{ $cfg['label'] }}">
                            <i class="fas {{ $cfg['icon'] }}"></i>
                            <div class="flow-value">{{ $cfg['label'] }}</div>
                            <span class="flow-count">{{ $jenisCounts[$jenis] ?? 0 }} program</span>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-list mr-2"></i>Daftar Program</h5>
        <div class="d-flex">
            <form class="form-inline mr-2" method="GET">
                <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Tanggal mulai dari">
                <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Tanggal mulai sampai">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari program..."
                       value="{{ request('search') }}" style="border-radius:8px;width:200px;">
                <button class="btn btn-sm btn-outline-success ml-1"><i class="fas fa-search"></i></button>
                @if(request()->hasAny(['search','jenis_program','from_date','to_date']))
                <a href="{{ route('food-programs.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                @endif
            </form>
            <select class="form-control form-control-sm" style="width:120px;border-radius:8px;" onchange="filterType(this.value)">
                <option value="">Semua Jenis</option>
                <option value="pertanian" {{ request('jenis_program')=='pertanian'?'selected':'' }}>Pertanian</option>
                <option value="peternakan" {{ request('jenis_program')=='peternakan'?'selected':'' }}>Peternakan</option>
                <option value="perikanan" {{ request('jenis_program')=='perikanan'?'selected':'' }}>Perikanan</option>
                <option value="integrated" {{ request('jenis_program')=='integrated'?'selected':'' }}>Integrated</option>
                <option value="pengolahan" {{ request('jenis_program')=='pengolahan'?'selected':'' }}>Pengolahan</option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Program</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Tanggal Mulai</th>
                        <th>Anggaran</th>
                        <th>Peserta</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $program)
                    <tr>
                        <td><span class="badge badge-success" style="font-family:monospace;font-size:11px;">{{ $program->kode_program }}</span></td>
                        <td style="font-weight:600;">{{ $program->nama_program }}</td>
                        <td><span class="badge badge-info">{{ ucfirst($program->jenis_program) }}</span></td>
                        <td>
                            @php $sc = ['perencanaan'=>'warning','aktif'=>'success','selesai'=>'secondary','dibatalkan'=>'danger']; @endphp
                            <span class="badge badge-{{ $sc[$program->status] ?? 'secondary' }}">{{ ucfirst($program->status) }}</span>
                        </td>
                        <td style="font-size:12px;">{{ $program->tanggal_mulai->format('d/m/Y') }}</td>
                        <td style="font-size:12px;">Rp {{ number_format($program->anggaran,0,',','.') }}</td>
                        @php
                            $farmP = $program->farmingPlans->sum(fn($fp) => $fp->participants->count());
                            $liveP = $program->livestockBarns->sum(fn($b) => $b->participants->count());
                            $fishP = $program->fishPonds->sum(fn($p) => $p->participants->count());
                        @endphp
                        <td><span class="badge badge-primary">{{ $farmP + $liveP + $fishP }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('food-programs.show', array_merge([$program], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('food-programs.edit', $program) }}" class="btn btn-outline-success"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('food-programs.destroy', $program) }}','{{ $program->nama_program }}')"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-seedling fa-3x mb-3" style="color:#cbd5e1;"></i>
                            <p style="color:#94a3b8;">Belum ada program</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($programs->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $programs->firstItem() }}-{{ $programs->lastItem() }} dari {{ $programs->total() }}</small>
        {{ $programs->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@section('extra_js')
<script>
function filterType(val) {
    const url = new URL(window.location);
    if (val) url.searchParams.set('jenis_program', val); else url.searchParams.delete('jenis_program');
    window.location = url;
}
</script>
@endsection

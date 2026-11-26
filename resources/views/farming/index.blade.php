@extends('adminlte::master')

@section('title', 'Rencana Kerja Pertanian')

@section('header_actions')
<a href="{{ route('reports.farming', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-file-pdf mr-1"></i> Export PDF
</a>
<a href="{{ route('farming-plans.create') }}" class="btn btn-success btn-sm">
    <i class="fas fa-plus mr-1"></i> Tambah Rencana
</a>
@endsection

@section('content')
{{-- Farm Summary Cards --}}
<div class="row mb-4 dash-row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-map"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['lahan_tersedia']) }}</div>
                    <div class="stat-label">Lahan Tersedia</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fef3c7;color:#d97706;width:40px;height:40px;font-size:16px;"><i class="fas fa-seedling"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['rencana_aktif']) }}</div>
                    <div class="stat-label">Sedang Ditanam</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-apple-alt"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['panen_berat'],0,',','.') }} kg</div>
                    <div class="stat-label">Panen Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #8b5cf6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#ede9fe;color:#7c3aed;width:40px;height:40px;font-size:16px;"><i class="fas fa-money-bill"></i></div>
                <div>
                    <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['panen_pendapatan'],0,',','.') }}</div>
                    <div class="stat-label">Pendapatan Panen</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-tractor mr-2"></i>Rencana Kerja Pertanian</h5>
        <div class="d-flex">
            <form class="form-inline mr-2" method="GET">
                <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Rencana tanam dari">
                <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Rencana tanam sampai">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode rencana..." value="{{ request('search') }}" style="border-radius:8px;width:200px;">
                <button class="btn btn-sm btn-outline-success ml-1"><i class="fas fa-search"></i></button>
                @if(request()->hasAny(['search','status','from_date','to_date']))
                <a href="{{ route('farming-plans.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                @endif
            </form>
            <select class="form-control form-control-sm" style="width:120px;border-radius:8px;" onchange="filterStatus(this.value)">
                <option value="">Semua Status</option>
                <option value="direncanakan" {{ request('status')=='direncanakan'?'selected':'' }}>Direncanakan</option>
                <option value="proses_tanam" {{ request('status')=='proses_tanam'?'selected':'' }}>Proses Tanam</option>
                <option value="tumbuh" {{ request('status')=='tumbuh'?'selected':'' }}>Tumbuh</option>
                <option value="siap_panen" {{ request('status')=='siap_panen'?'selected':'' }}>Siap Panen</option>
                <option value="panen" {{ request('status')=='panen'?'selected':'' }}>Panen</option>
                <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Lahan</th>
                        <th>Tanaman</th>
                        <th>Metode</th>
                        <th>Tgl Tanam</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td><span class="badge badge-success" style="font-family:monospace;font-size:11px;">{{ $plan->kode_rencana }}</span></td>
                        <td>{{ $plan->landPlot?->nama_lahan ?? '-' }}</td>
                        <td style="font-weight:600;">{{ $plan->cropType?->nama_tanaman ?? '-' }}</td>
                        <td><span class="badge badge-secondary">{{ ucfirst($plan->metode_budi_daya) }}</span></td>
                        <td style="font-size:12px;">{{ $plan->tanggal_tanam_rencana?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ number_format($plan->jumlah_tanaman) }}</td>
                        <td>
                            @php $fsc = ['direncanakan'=>'secondary','proses_tanam'=>'info','tumbuh'=>'success','siap_panen'=>'warning','panen'=>'primary','selesai'=>'dark']; @endphp
                            <span class="badge badge-{{ $fsc[$plan->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$plan->status)) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('farming-plans.show', array_merge([$plan], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('farming-plans.edit', $plan) }}" class="btn btn-outline-success"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-tractor fa-3x mb-3" style="color:#cbd5e1;"></i>
                            <p style="color:#94a3b8;">Belum ada rencana kerja</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($plans->hasPages())
    <div class="card-footer">
        {{ $plans->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@section('extra_js')
<script>
function filterStatus(val) {
    const url = new URL(window.location);
    if (val) url.searchParams.set('status', val); else url.searchParams.delete('status');
    window.location = url;
}
</script>
@endsection

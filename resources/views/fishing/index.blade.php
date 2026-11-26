@extends('adminlte::master')

@section('title', 'Perikanan')

@section('header_actions')
<a href="{{ route('reports.fishery', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-file-pdf mr-1"></i> Export PDF
</a>
<a href="{{ route('fish-ponds.create') }}" class="btn btn-success btn-sm">
    <i class="fas fa-plus mr-1"></i> Tambah Kolam
</a>
@endsection

@section('content')
<div class="row mb-4 dash-row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-water"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['kolam_aktif']) }}</div>
                    <div class="stat-label">Kolam Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-fish"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['stok_hidup']) }}</div>
                    <div class="stat-label">Total Ikan Hidup</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #06b6d4;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#cffafe;color:#0891b2;width:40px;height:40px;font-size:16px;"><i class="fas fa-tint"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['kualitas_hari_ini']) }}</div>
                    <div class="stat-label">Pemeriksaan Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fef3c7;color:#d97706;width:40px;height:40px;font-size:16px;"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['panen_berat'],0,',','.') }} kg</div>
                    <div class="stat-label">Panen Ikan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-water mr-2"></i>Daftar Kolam</h5>
        <div class="d-flex">
            <form class="form-inline mr-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kolam..." value="{{ request('search') }}" style="border-radius:8px;width:200px;">
                <button class="btn btn-sm btn-outline-success ml-1"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kolam</th>
                        <th>Tipe</th>
                        <th>Luas</th>
                        <th>Sumber Air</th>
                        <th>Stok Ikan</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ponds as $pond)
                    <tr>
                        <td><span class="badge badge-primary" style="font-family:monospace;font-size:11px;">{{ $pond->kode_kolam }}</span></td>
                        <td style="font-weight:600;">{{ $pond->nama_kolam }}</td>
                        <td><span class="badge badge-info">{{ ucfirst(str_replace('_',' ',$pond->tipe_kolam)) }}</span></td>
                        <td>{{ number_format($pond->luas_m2,0,',','.') }} m²</td>
                        <td>{{ ucfirst($pond->sumber_air) }}</td>
                        <td>
                            @php $totalFish = $pond->fishStocks->where('status','hidup')->sum('jumlah'); @endphp
                            <span class="badge badge-{{ $totalFish > 0 ? 'success' : 'secondary' }}">{{ number_format($totalFish) }} ekor</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $pond->status == 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($pond->status) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('fish-ponds.show', array_merge([$pond], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('fish-ponds.edit', $pond) }}" class="btn btn-outline-success"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-water fa-3x mb-3" style="color:#cbd5e1;"></i>
                            <p style="color:#94a3b8;">Belum ada kolam</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ponds->hasPages())
    <div class="card-footer">{{ $ponds->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

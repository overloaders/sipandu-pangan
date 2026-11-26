@extends('adminlte::master')

@section('title', 'Peternakan')

@section('header_actions')
<a href="{{ route('reports.livestock', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-file-pdf mr-1"></i> Export PDF
</a>
<a href="{{ route('livestock-barns.create') }}" class="btn btn-success btn-sm">
    <i class="fas fa-plus mr-1"></i> Tambah Kandang
</a>
@endsection

@section('content')
<div class="row mb-4 dash-row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-warehouse"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ $stats['kandang_aktif'] }}</div>
                    <div class="stat-label">Kandang Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-paw"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ $stats['ternak_hidup'] }}</div>
                    <div class="stat-label">Total Ternak Hidup</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fef3c7;color:#d97706;width:40px;height:40px;font-size:16px;"><i class="fas fa-drumstick-bite"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ $stats['ternak_panen'] }}</div>
                    <div class="stat-label">Dipanen</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #ec4899;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fce7f3;color:#db2777;width:40px;height:40px;font-size:16px;"><i class="fas fa-heartbeat"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ $stats['periksa_minggu_ini'] }}</div>
                    <div class="stat-label">Pemeriksaan Minggu Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-warehouse mr-2"></i>Daftar Kandang</h5>
        <div class="d-flex">
            <form class="form-inline mr-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kandang..." value="{{ request('search') }}" style="border-radius:8px;width:200px;">
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
                        <th>Nama Kandang</th>
                        <th>Jenis Ternak</th>
                        <th>Tipe</th>
                        <th>Kapasitas</th>
                        <th>Terisi</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barns as $barn)
                    <tr>
                        <td><span class="badge badge-success" style="font-family:monospace;font-size:11px;">{{ $barn->kode_kandang }}</span></td>
                        <td style="font-weight:600;">{{ $barn->nama_kandang }}</td>
                        <td><span class="badge badge-info">{{ ucfirst($barn->jenis_ternak) }}</span></td>
                        <td style="font-size:12px;">{{ ucfirst(str_replace('_',' ',$barn->tipe_kandang)) }}</td>
                        <td>{{ $barn->kapasitas }}</td>
                        <td>
                            @php $pct = $barn->kapasitas > 0 ? ($barn->terisi/$barn->kapasitas*100) : 0; @endphp
                            <div class="progress" style="height:6px;width:80px;">
                                <div class="progress-bar bg-{{ $pct > 80 ? 'danger' : ($pct > 50 ? 'warning' : 'success') }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="text-muted">{{ $barn->terisi }}/{{ $barn->kapasitas }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $barn->status == 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($barn->status) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('livestock-barns.show', array_merge([$barn], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('livestock-barns.edit', $barn) }}" class="btn btn-outline-success"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-warehouse fa-3x mb-3" style="color:#cbd5e1;"></i>
                            <p style="color:#94a3b8;">Belum ada kandang</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($barns->hasPages())
    <div class="card-footer">{{ $barns->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

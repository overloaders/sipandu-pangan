@extends('adminlte::master')

@section('title', 'Data Hasil Panen')

@section('header_actions')
<a href="{{ route('reports.harvest', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-file-pdf mr-1"></i> Export PDF
</a>
<a href="{{ route('harvests.create') }}" class="btn btn-success btn-sm">
    <i class="fas fa-plus mr-1"></i> Tambah Panen
</a>
@endsection

@section('content')
{{-- Summary Cards --}}
<div class="row mb-4 dash-row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-weight-hanging"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($totalPanen ?? 0, 0, ',', '.') }} kg</div>
                    <div class="stat-label">Total Panen</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($panenBulanIni ?? 0, 0, ',', '.') }} kg</div>
                    <div class="stat-label">Panen Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #8b5cf6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#ede9fe;color:#7c3aed;width:40px;height:40px;font-size:16px;"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <div class="stat-value" style="font-size:18px;">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fef3c7;color:#d97706;width:40px;height:40px;font-size:16px;"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="stat-value" style="font-size:18px;">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Pendapatan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-apple-alt mr-2"></i>Data Hasil Panen</h5>
        <div class="d-flex flex-wrap">
            <form class="form-inline mr-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode panen..." value="{{ request('search') }}" style="border-radius:8px;width:180px;">
                <select name="kualitas" class="form-control form-control-sm ml-1" style="border-radius:8px;width:100px;">
                    <option value="">Semua Kualitas</option>
                    @foreach(['A','B','C','D'] as $k)
                    <option value="{{ $k }}" {{ request('kualitas')==$k?'selected':'' }}>Kualitas {{ $k }}</option>
                    @endforeach
                </select>
                <input type="date" name="from_date" class="form-control form-control-sm ml-1" value="{{ request('from_date') }}" style="border-radius:8px;width:140px;" placeholder="Dari">
                <input type="date" name="to_date" class="form-control form-control-sm ml-1" value="{{ request('to_date') }}" style="border-radius:8px;width:140px;" placeholder="Sampai">
                <button class="btn btn-sm btn-outline-success ml-1"><i class="fas fa-search"></i></button>
                @if(request()->hasAny(['search','kualitas','from_date','to_date']))
                <a href="{{ route('harvests.index') }}" class="btn btn-sm btn-outline-secondary ml-1"><i class="fas fa-times"></i></a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Kode Panen</th>
                    <th>Tanggal</th>
                    <th>Rencana Kerja</th>
                    <th>Tanaman</th>
                    <th>Lahan</th>
                    <th>Berat (kg)</th>
                    <th>Harga/kg</th>
                    <th>Pendapatan</th>
                    <th>Kualitas</th>
                    <th style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($harvests as $i => $h)
                <tr>
                    <td>{{ ($harvests->currentPage()-1)*$harvests->perPage()+$i+1 }}</td>
                    <td><strong>{{ $h->kode_panen }}</strong></td>
                    <td>{{ $h->tanggal_panen?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $h->farmingPlan->kode_rencana ?? '-' }}</td>
                    <td>{{ $h->farmingPlan->cropType->nama_tanaman ?? '-' }}</td>
                    <td>{{ $h->farmingPlan->landPlot->nama_lahan ?? '-' }}</td>
                    <td><strong>{{ number_format($h->total_berat_kg, 0, ',', '.') }}</strong></td>
                    <td>Rp {{ number_format($h->harga_jual_per_kg, 0, ',', '.') }}</td>
                    <td style="color:#059669;font-weight:600;">Rp {{ number_format($h->total_pendapatan, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $colors = ['A'=>'#059669','B'=>'#2563eb','C'=>'#d97706','D'=>'#dc2626'];
                        @endphp
                        <span class="badge" style="background:{{ $colors[$h->kualitas] ?? '#6b7280' }};color:#fff;padding:4px 10px;border-radius:12px;font-size:12px;">
                            {{ $h->kualitas }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('harvests.show', array_merge([$h], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-xs btn-info" title="Detail"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('harvests.edit', $h) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-xs btn-danger" title="Hapus"
                            onclick="confirmDelete('{{ route('harvests.destroy', $h) }}', '{{ $h->kode_panen }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block" style="color:#cbd5e1;"></i>
                        Belum ada data hasil panen
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($harvests->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $harvests->firstItem() }}-{{ $harvests->lastItem() }} dari {{ $harvests->total() }} data</small>
        {{ $harvests->links() }}
    </div>
    @endif
</div>
@endsection

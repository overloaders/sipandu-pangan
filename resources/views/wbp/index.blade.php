@extends('adminlte::master')

@section('title', 'Registrasi WBP')

@section('header_actions')
<a href="{{ route('reports.wbp', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-file-pdf mr-1"></i> Export PDF
</a>
<a href="{{ route('wbp.create') }}" class="btn btn-success btn-sm">
    <i class="fas fa-plus mr-1"></i> Tambah WBP
</a>
@endsection

@section('content')
{{-- Summary Cards --}}
<div class="row mb-4 dash-row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ $wbp->total() }}</div>
                    <div class="stat-label">Total WBP</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;">
                    <i class="fas fa-user-check"></i>
                </div>
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
                <div class="stat-icon mr-3" style="background:#fef3c7;color:#d97706;width:40px;height:40px;font-size:16px;">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['baru']) }}</div>
                    <div class="stat-label">Baru Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #ec4899;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fce7f3;color:#db2777;width:40px;height:40px;font-size:16px;">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($stats['keluar']) }}</div>
                    <div class="stat-label">Dikeluarkan</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Daftar Warga Binaan Pemasyarakatan</h5>
        <div class="d-flex align-items-center">
            <form class="form-inline mr-2" method="GET">
                <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Tanggal masuk dari">
                <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Tanggal masuk sampai">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari nomor induk, nama, NIK..."
                           value="{{ request('search') }}" style="border-radius:8px 0 0 8px;width:250px;">
                    <div class="input-group-append">
                        <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->hasAny(['search','status','from_date','to_date']))
                <a href="{{ route('wbp.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                @endif
            </form>
            <div class="btn-group btn-group-sm">
                <select class="form-control form-control-sm" style="border-radius:8px;width:120px;" onchange="filterStatus(this.value)">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="dikeluarkan" {{ request('status')=='dikeluarkan'?'selected':'' }}>Dikeluarkan</option>
                    <option value="pindah" {{ request('status')=='pindah'?'selected':'' }}>Pindah</option>
                    <option value="bebas" {{ request('status')=='bebas'?'selected':'' }}>Bebas</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" class="form-check-input"></th>
                        <th>No. Induk</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Usia</th>
                        <th>Blok/Kamar</th>
                        <th>Status</th>
                        <th>Masuk</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wbp as $item)
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <span class="badge badge-success" style="font-family:monospace;font-size:12px;">
                                {{ $item->nomor_induk }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mr-2"
                                     style="width:32px;height:32px;background:{{ $item->jenis_kelamin=='L' ? '#dbeafe' : '#fce7f3' }};color:{{ $item->jenis_kelamin=='L' ? '#2563eb' : '#db2777' }};font-size:12px;font-weight:600;">
                                    {{ strtoupper(substr($item->nama_lengkap,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $item->nama_lengkap }}</div>
                                    <div style="font-size:11px;color:#94a3b8;">NIK: {{ $item->nik }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $item->jenis_kelamin=='L' ? 'badge-info' : 'badge-danger' }}">
                                {{ $item->jenis_kelamin }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_lahir)->age }} th</td>
                        <td>
                            <span class="badge badge-secondary">{{ $item->block?->kode_blok ?? '-' }}/{{ $item->room?->kode_kamar ?? '-' }}</span>
                        </td>
                        <td>
                            @php $statusColors = ['aktif'=>'success','dikeluarkan'=>'danger','pindah'=>'warning','meninggal'=>'dark','bebas'=>'info']; @endphp
                            <span class="badge badge-{{ $statusColors[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span>
                            @if($item->status_histories_count > 0)
                                <a href="{{ route('wbp.show', array_merge([$item], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="badge badge-light border ml-1" title="{{ $item->status_histories_count }}x perubahan status" style="font-size:11px;"><i class="fas fa-history"></i> {{ $item->status_histories_count }}</a>
                            @endif
                        </td>
                        <td style="font-size:12px;">{{ $item->tanggal_masuk->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('wbp.show', array_merge([$item], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-outline-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('wbp.edit', $item) }}" class="btn btn-outline-success" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('wbp.destroy', $item) }}','{{ $item->nama_lengkap }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x mb-3" style="color:#cbd5e1;"></i>
                            <p style="color:#94a3b8;">Belum ada data WBP</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($wbp->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $wbp->firstItem() }}-{{ $wbp->lastItem() }} dari {{ $wbp->total() }} data</small>
        {{ $wbp->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@section('extra_js')
<script>
function filterStatus(val) {
    const url = new URL(window.location);
    if (val) url.searchParams.set('status', val);
    else url.searchParams.delete('status');
    window.location = url;
}
</script>
@endsection

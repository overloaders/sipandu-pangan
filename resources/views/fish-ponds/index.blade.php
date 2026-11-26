@extends('adminlte::page')
@section('title', 'Perikanan - Kolam')
@section('header_actions')
    <a href="{{ route('reports.fishery', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <a href="{{ route('fish-ponds.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Kolam</a>
@endsection
@section('content')
<div class="card dash-row">
    <div class="card-header">
        <form action="{{ route('fish-ponds.index') }}" method="GET" class="form-inline">
            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari kode/nama..." value="{{ request('search') }}">
            <select name="status" class="form-control form-control-sm mr-2"><option value="">Semua Status</option><option value="aktif">Aktif</option><option value="kering">Kering</option><option value="renovasi">Renovasi</option><option value="nonaktif">Nonaktif</option></select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead><tr><th>#</th><th>Kode</th><th>Nama Kolam</th><th>Luas (m²)</th><th>Volume (m³)</th><th>Tipe</th><th>Sumber Air</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($ponds as $i => $p)
                <tr>
                    <td>{{ $ponds->firstItem()+$i }}</td>
                    <td><code>{{ $p->kode_kolam }}</code></td>
                    <td>{{ $p->nama_kolam }}</td>
                    <td>{{ number_format($p->luas_m2, 2) }}</td>
                    <td>{{ number_format($p->volume_m3 ?? 0, 2) }}</td>
                    <td><span class="badge badge-info">{{ $p->tipe_kolam ?? '-' }}</span></td>
                    <td>{{ $p->sumber_air ?? '-' }}</td>
                    <td><span class="badge badge-{{ $p->status=='aktif'?'success':'warning' }}">{{ $p->status }}</span></td>
                    <td>
                        <a href="{{ route('fish-ponds.show', array_merge([$p], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('fish-ponds.edit', $p) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('{{ route('fish-ponds.destroy', $p) }}','{{ $p->nama_kolam }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty <tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data</td></tr> @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $ponds->withQueryString()->links() }}</div>
</div>
@stop

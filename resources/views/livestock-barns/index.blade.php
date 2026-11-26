@extends('adminlte::page')
@section('title', 'Peternakan - Kandang')
@section('header_actions')
    <a href="{{ route('reports.livestock', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <a href="{{ route('livestock-barns.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Kandang</a>
@endsection
@section('content')
<div class="card dash-row">
    <div class="card-header">
        <form action="{{ route('livestock-barns.index') }}" method="GET" class="form-inline">
            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari kode/nama..." value="{{ request('search') }}">
            <select name="status" class="form-control form-control-sm mr-2"><option value="">Semua Status</option><option value="aktif">Aktif</option><option value="renovasi">Renovasi</option><option value="nonaktif">Nonaktif</option></select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead><tr><th>#</th><th>Kode</th><th>Nama Kandang</th><th>Jenis</th><th>Kapasitas</th><th>Terisi</th><th>Tipe</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($barns as $i => $b)
                <tr>
                    <td>{{ $barns->firstItem()+$i }}</td>
                    <td><code>{{ $b->kode_kandang }}</code></td>
                    <td>{{ $b->nama_kandang }}</td>
                    <td><span class="badge badge-info">{{ $b->jenis_ternak }}</span></td>
                    <td>{{ $b->kapasitas }}</td>
                    <td>{{ $b->terisi }}</td>
                    <td>{{ $b->tipe_kandang ?? '-' }}</td>
                    <td><span class="badge badge-{{ $b->status=='aktif'?'success':'warning' }}">{{ $b->status }}</span></td>
                    <td>
                        <a href="{{ route('livestock-barns.show', array_merge([$b], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('livestock-barns.edit', $b) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('{{ route('livestock-barns.destroy', $b) }}','{{ $b->nama_kandang }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty <tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data</td></tr> @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $barns->withQueryString()->links() }}</div>
</div>
@stop

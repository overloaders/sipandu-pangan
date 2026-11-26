@extends('adminlte::page')
@section('title', 'Distribusi')
@section('header_actions')
    <a href="{{ route('reports.distributions', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <a href="{{ route('distributions.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Distribusi</a>
@endsection
@section('content')
<div class="card dash-row">
    <div class="card-header">
        <form action="{{ route('distributions.index') }}" method="GET" class="form-inline">
            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari kode/nama..." value="{{ request('search') }}">
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="dikirim" {{ request('status')=='dikirim'?'selected':'' }}>Dikirim</option>
                <option value="diterima" {{ request('status')=='diterima'?'selected':'' }}>Diterima</option>
                <option value="dibatalkan" {{ request('status')=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
            </select>
            <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Dari tanggal">
            <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Sampai tanggal">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            @if(request()->hasAny(['search','status','from_date','to_date']))
            <a href="{{ route('distributions.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead><tr><th>#</th><th>Kode</th><th>Penerima</th><th>Tipe</th><th>Tanggal</th><th>Nilai</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($distributions as $i => $d)
                <tr>
                    <td>{{ $distributions->firstItem()+$i }}</td>
                    <td><code>{{ $d->kode_distribusi }}</code></td>
                    <td>{{ $d->penerima_nama ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($d->tipe_distribusi)) }}</span></td>
                    <td>{{ $d->tanggal_distribusi->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($d->total_nilai, 0, ',', '.') }}</td>
                    <td><span class="badge badge-{{ $d->status=='diterima'?'success':($d->status=='dibatalkan'?'danger':($d->status=='dikirim'?'primary':'warning')) }}">{{ ucfirst($d->status) }}</span></td>
                    <td>
                        <a href="{{ route('distributions.show', array_merge([$d], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('distributions.edit', $d) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('{{ route('distributions.destroy', $d) }}','{{ $d->kode_distribusi }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $distributions->withQueryString()->links() }}</div>
</div>
@stop

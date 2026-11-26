@extends('adminlte::page')
@section('title', 'Master Unit')
@section('content_header')
    <h1><i class="fas fa-building"></i> Master Unit</h1>
@endsection
@section('header_actions')
    <a href="{{ route('reports.master-units', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <a href="{{ route('master.units.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Unit</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-building mr-2"></i>Daftar Unit</h5>
        <div class="card-tools">
            <form action="{{ route('master.units.index') }}" method="GET" class="form-inline">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>No</th><th>Kode</th><th>Nama Unit</th><th>Tipe</th><th>Provinsi</th><th>Kota</th><th>Telepon</th><th>Kapasitas WBP</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($units as $u)
                <tr>
                    <td>{{ $units->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $u->kode_unit }}</span></td>
                    <td>
                        @if($u->logo_url)
                            <img src="{{ $u->logo_url }}" alt="Logo" style="height:22px;width:22px;object-fit:contain;vertical-align:middle;margin-right:6px;border-radius:3px;">
                        @endif
                        <strong>{{ $u->nama_unit }}</strong>
                    </td>
                    <td><span class="badge badge-secondary">{{ ucfirst($u->tipe_unit) }}</span></td>
                    <td>{{ $u->provinsi ?? '-' }}</td>
                    <td>{{ $u->kota_kab ?? '-' }}</td>
                    <td>{{ $u->telepon ?? '-' }}</td>
                    <td>{{ $u->kapasitas_wbp ?? '-' }}</td>
                    <td><span class="badge badge-{{ $u->status=='aktif'?'success':'secondary' }}">{{ ucfirst($u->status) }}</span></td>
                    <td>
                        <a href="{{ route('master.units.edit', $u) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-unit-{{ $u->id }}" action="{{ route('master.units.destroy', $u) }}" method="POST" class="d-inline" >@csrf @method('DELETE')<button type="button" class="btn btn-danger btn-xs" onclick="confirmDeleteForm(this.closest('form').id)"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center">Belum ada data unit</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $units->withQueryString()->links() }}</div>
</div>
@endsection

@extends('adminlte::page')
@section('title', 'Master Kategori Keuangan')
@section('content_header')
    <h1><i class="fas fa-wallet"></i> Master Kategori Keuangan</h1>
@endsection
@section('header_actions')
    <a href="{{ route('reports.master-financial-categories', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <a href="{{ route('master.financial-categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Kategori</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-wallet mr-2"></i>Daftar Kategori Keuangan</h5>
        <div class="card-tools">
            <form action="{{ route('master.financial-categories.index') }}" method="GET" class="form-inline">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>No</th><th>Kode</th><th>Nama Kategori</th><th>Tipe</th><th>Keterangan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($categories as $c)
                <tr>
                    <td>{{ $categories->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $c->kode_kategori_keu }}</span></td>
                    <td><strong>{{ $c->nama_kategori }}</strong></td>
                    <td><span class="badge badge-{{ $c->tipe=='pemasukan'?'success':'danger' }}">{{ ucfirst($c->tipe) }}</span></td>
                    <td>{{ $c->keterangan ?? '-' }}</td>
                    <td><span class="badge badge-{{ $c->is_active?'success':'secondary' }}">{{ $c->is_active ? 'Aktif' : 'Non-aktif' }}</span></td>
                    <td>
                        <a href="{{ route('master.financial-categories.edit', $c) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-fin-cat-{{ $c->id }}" action="{{ route('master.financial-categories.destroy', $c) }}" method="POST" class="d-inline" >@csrf @method('DELETE')<button type="button" class="btn btn-danger btn-xs" onclick="confirmDeleteForm(this.closest('form').id)"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">Belum ada data kategori keuangan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $categories->withQueryString()->links() }}</div>
</div>
@endsection

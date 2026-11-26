@extends('adminlte::page')
@section('title', 'Master Jenis Tanaman')
@section('content_header')
    <h1><i class="fas fa-seedling"></i> Master Jenis Tanaman</h1>
@endsection
@section('header_actions')
    <a href="{{ route('reports.master-crop-types', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <a href="{{ route('master.crop-types.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Jenis</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-seedling mr-2"></i>Daftar Jenis Tanaman</h5>
        <div class="card-tools">
            <form action="{{ route('master.crop-types.index') }}" method="GET" class="form-inline">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead><tr><th>No</th><th>Kode</th><th>Nama Tanaman</th><th>Nama Latin</th><th>Kategori</th><th>Masa Tanam</th><th>Hasil/m²</th><th>Harga Jual/kg</th><th>Musim</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($types as $t)
                <tr>
                    <td>{{ $types->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $t->kode_tanaman }}</span></td>
                    <td><strong>{{ $t->nama_tanaman }}</strong></td>
                    <td><em>{{ $t->nama_latin ?? '-' }}</em></td>
                    <td><span class="badge badge-secondary">{{ ucfirst($t->kategori) }}</span></td>
                    <td>{{ $t->masa_tanam_hari ?? '-' }} hari</td>
                    <td>{{ $t->hasil_per_m2_kg ?? '-' }} kg</td>
                    <td>Rp {{ number_format($t->harga_jual_per_kg ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $t->musim_tanam ?? '-' }}</td>
                    <td>
                        <a href="{{ route('master.crop-types.edit', $t) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-crop-{{ $t->id }}" action="{{ route('master.crop-types.destroy', $t) }}" method="POST" class="d-inline" >@csrf @method('DELETE')<button type="button" class="btn btn-danger btn-xs" onclick="confirmDeleteForm(this.closest('form').id)"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center">Belum ada data jenis tanaman</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $types->withQueryString()->links() }}</div>
</div>
@endsection

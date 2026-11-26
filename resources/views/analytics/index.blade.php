@extends('adminlte::page')

@section('title', 'Analitik & Indikator')

@section('content_header')
    <h1><i class="fas fa-chart-line"></i> Analitik & Indikator</h1>
@endsection

@section('header_actions')
    <a href="{{ route('analytics.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Indikator
    </a>
    <a href="{{ route('analytics.dashboard') }}" class="btn btn-info btn-sm">
        <i class="fas fa-chart-bar"></i> Dashboard
    </a>
    <a href="{{ route('reports.analytics', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="card dash-row">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-line mr-2"></i>Daftar Indikator Analitik</h5>
        <div class="card-tools">
            <form action="{{ route('analytics.index') }}" method="GET" class="form-inline">
                <select name="kategori" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Kategori</option>
                    <option value="produktivitas" {{ request('kategori')=='produktivitas'?'selected':'' }}>Produktivitas</option>
                    <option value="efisiensi" {{ request('kategori')=='efisiensi'?'selected':'' }}>Efisiensi</option>
                    <option value="kualitas_pangan" {{ request('kategori')=='kualitas_pangan'?'selected':'' }}>Kualitas Pangan</option>
                    <option value="keuangan" {{ request('kategori')=='keuangan'?'selected':'' }}>Keuangan</option>
                    <option value="sdm" {{ request('kategori')=='sdm'?'selected':'' }}>SDM</option>
                </select>
                <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Non-aktif</option>
                </select>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'kategori', 'status']))
                <a href="{{ route('analytics.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Indikator</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Target</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analytics as $a)
                <tr>
                    <td>{{ $analytics->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $a->kode_indikator }}</span></td>
                    <td>{{ $a->nama_indikator }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst($a->kategori) }}</span></td>
                    <td>{{ $a->satuan }}</td>
                    <td>{{ number_format($a->target_nilai ?? 0, 2) }}</td>
                    <td>
                        @if($a->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Non-aktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('analytics.show', array_merge([$a->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('analytics.edit', $a->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-form-1" action="{{ route('analytics.destroy', $a->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm('delete-form-1')" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada data indikator</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $analytics->withQueryString()->links() }}
    </div>
</div>
@endsection

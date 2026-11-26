@extends('adminlte::page')

@section('title', 'Program Pelatihan')

@section('content_header')
    <h1><i class="fas fa-graduation-cap"></i> Program Pelatihan</h1>
@endsection

@section('header_actions')
    <a href="{{ route('training.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Program
    </a>
    <a href="{{ route('reports.training', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="card dash-row">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-graduation-cap mr-2"></i>Daftar Program Pelatihan</h5>
        <div class="card-tools">
            <form action="{{ route('training.index') }}" method="GET" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="perencanaan" {{ request('status')=='perencanaan'?'selected':'' }}>Perencanaan</option>
                    <option value="terbuka" {{ request('status')=='terbuka'?'selected':'' }}>Terbuka</option>
                    <option value="berlangsung" {{ request('status')=='berlangsung'?'selected':'' }}>Berlangsung</option>
                    <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status')=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
                </select>
                <select name="tipe" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Tipe</option>
                    <option value="offline" {{ request('tipe')=='offline'?'selected':'' }}>Offline</option>
                    <option value="online" {{ request('tipe')=='online'?'selected':'' }}>Online</option>
                    <option value="hybrid" {{ request('tipe')=='hybrid'?'selected':'' }}>Hybrid</option>
                </select>
                <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Dari tanggal">
                <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Sampai tanggal">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'status', 'tipe', 'from_date', 'to_date']))
                <a href="{{ route('training.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
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
                    <th>Nama Pelatihan</th>
                    <th>Kategori</th>
                    <th>Tipe</th>
                    <th>Lokasi</th>
                    <th>Tanggal Mulai</th>
                    <th>Peserta</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $p)
                <tr>
                    <td>{{ $programs->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $p->kode_pelatihan }}</span></td>
                    <td>{{ $p->nama_pelatihan }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst($p->kategori) }}</span></td>
                    <td><span class="badge badge-primary">{{ ucfirst($p->tipe ?? '-') }}</span></td>
                    <td>{{ $p->lokasi ?? '-' }}</td>
                    <td>{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $p->participants_count ?? $p->participants->count() }}</td>
                    <td>
                        @if($p->status == 'aktif')
                            <span class="badge badge-success">Aktif</span>
                        @elseif($p->status == 'selesai')
                            <span class="badge badge-warning">Selesai</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($p->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('training.show', array_merge([$p->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('training.edit', $p->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-form-1" action="{{ route('training.destroy', $p->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm('delete-form-1')" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">Belum ada data program pelatihan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $programs->withQueryString()->links() }}
    </div>
</div>
@endsection

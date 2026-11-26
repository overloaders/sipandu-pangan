@extends('adminlte::page')

@section('title', 'Nutrisi & Menu')

@section('content_header')
    <h1><i class="fas fa-utensils"></i> Nutrisi & Menu</h1>
@endsection

@section('header_actions')
    <a href="{{ route('nutrition.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Menu
    </a>
    <a href="{{ route('reports.nutrition', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="card dash-row">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-utensils mr-2"></i>Daftar Rencana Gizi</h5>
        <div class="card-tools">
            <form action="{{ route('nutrition.index') }}" method="GET" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Non-aktif</option>
                </select>
                <select name="tipe_porsi" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Tipe Porsi</option>
                    <option value="vegan" {{ request('tipe_porsi')=='vegan'?'selected':'' }}>Vegan</option>
                    <option value="lansia" {{ request('tipe_porsi')=='lansia'?'selected':'' }}>Lansia</option>
                    <option value="standar" {{ request('tipe_porsi')=='standar'?'selected':'' }}>Standar</option>
                    <option value="diet_khusus" {{ request('tipe_porsi')=='diet_khusus'?'selected':'' }}>Diet Khusus</option>
                    <option value="bayi" {{ request('tipe_porsi')=='bayi'?'selected':'' }}>Bayi</option>
                </select>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'status', 'tipe_porsi']))
                <a href="{{ route('nutrition.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
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
                    <th>Nama Menu</th>
                    <th>Waktu Makan</th>
                    <th>Tipe Porsi</th>
                    <th>Kalori</th>
                    <th>Protein</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $p)
                <tr>
                    <td>{{ $plans->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $p->kode_rencana_gizi }}</span></td>
                    <td>{{ $p->nama_menu }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst($p->waktu_makan) }}</span></td>
                    <td><span class="badge badge-primary">{{ ucfirst($p->tipe_porsi) }}</span></td>
                    <td>{{ $p->kalori_target_kcal ?? '-' }} kcal</td>
                    <td>{{ $p->protein_target_gr ?? '-' }} gr</td>
                    <td>
                        @if($p->status == 'aktif')
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($p->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('nutrition.show', array_merge([$p->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('nutrition.edit', $p->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-form-1" action="{{ route('nutrition.destroy', $p->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm('delete-form-1')" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada data rencana gizi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $plans->withQueryString()->links() }}
    </div>
</div>
@endsection

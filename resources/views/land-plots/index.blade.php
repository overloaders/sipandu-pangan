@extends('adminlte::page')

@section('title', 'Manajemen Lahan')

@section('content_header')
    <h1><i class="fas fa-map"></i> Manajemen Lahan</h1>
@endsection

@section('header_actions')
    <a href="{{ route('land-plots.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Lahan
    </a>
    <a href="{{ route('reports.land-plots', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="card dash-row">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-map mr-2"></i>Daftar Lahan</h5>
        <div class="card-tools">
            <form action="{{ route('land-plots.index') }}" method="GET" class="form-inline">
                <select name="jenis_tanah" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Jenis Tanah</option>
                    <option value="tanah_berpasir" {{ request('jenis_tanah')=='tanah_berpasir'?'selected':'' }}>Tanah Berpasir</option>
                    <option value="tanah_gambut" {{ request('jenis_tanah')=='tanah_gambut'?'selected':'' }}>Tanah Gambut</option>
                    <option value="campuran" {{ request('jenis_tanah')=='campuran'?'selected':'' }}>Campuran</option>
                    <option value="tanah_liat" {{ request('jenis_tanah')=='tanah_liat'?'selected':'' }}>Tanah Liat</option>
                    <option value="tanah_laterit" {{ request('jenis_tanah')=='tanah_laterit'?'selected':'' }}>Tanah Laterit</option>
                    <option value="tanah_volkanik" {{ request('jenis_tanah')=='tanah_volkanik'?'selected':'' }}>Tanah Volkanik</option>
                </select>
                <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status')=='tersedia'?'selected':'' }}>Tersedia</option>
                    <option value="ditanami" {{ request('status')=='ditanami'?'selected':'' }}>Ditanami</option>
                    <option value="istirahat" {{ request('status')=='istirahat'?'selected':'' }}>Istirahat</option>
                    <option value="renovasi" {{ request('status')=='renovasi'?'selected':'' }}>Renovasi</option>
                    <option value="tidak_digunakan" {{ request('status')=='tidak_digunakan'?'selected':'' }}>Tidak Digunakan</option>
                </select>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'jenis_tanah', 'status']))
                <a href="{{ route('land-plots.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Lahan</th>
                    <th>Nama Lahan</th>
                    <th>Unit</th>
                    <th>Luas (m²)</th>
                    <th>Jenis Tanah</th>
                    <th>Sistem Irigasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($landPlots as $lp)
                <tr>
                    <td>{{ $landPlots->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $lp->kode_lahan }}</span></td>
                    <td>{{ $lp->nama_lahan }}</td>
                    <td>{{ $lp->unit->nama_unit ?? '-' }}</td>
                    <td>{{ number_format($lp->luas_m2, 0, ',', '.') }} m²</td>
                    <td><span class="badge badge-secondary">{{ ucfirst($lp->jenis_tanah ?? '-') }}</span></td>
                    <td>{{ ucfirst($lp->sistem_irigasi ?? '-') }}</td>
                    <td>
                        @if($lp->status == 'tersedia')
                            <span class="badge badge-success">Tersedia</span>
                        @elseif(in_array($lp->status, ['ditanami', 'istirahat', 'renovasi']))
                            <span class="badge badge-warning">{{ ucfirst($lp->status) }}</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $lp->status)) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('land-plots.show', array_merge([$lp->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('land-plots.edit', $lp->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-form-1" action="{{ route('land-plots.destroy', $lp->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm('delete-form-1')" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada data lahan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $landPlots->withQueryString()->links() }}
    </div>
</div>
@endsection

@extends('adminlte::page')

@section('title', 'Stock Opname')

@section('content_header')
    <h1><i class="fas fa-clipboard-check"></i> Stock Opname</h1>
@endsection

@section('header_actions')
    <a href="{{ route('stock-opname.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Opname
    </a>
    <a href="{{ route('reports.stock-opname', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="card dash-row">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-clipboard-check mr-2"></i>Daftar Stock Opname</h5>
        <div class="card-tools">
            <form action="{{ route('stock-opname.index') }}" method="GET" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="proses" {{ request('status')=='proses'?'selected':'' }}>Proses</option>
                    <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status')=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
                </select>
                <select name="jenis_opname" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Jenis</option>
                    <option value="rutin" {{ request('jenis_opname')=='rutin'?'selected':'' }}>Rutin</option>
                    <option value="insidental" {{ request('jenis_opname')=='insidental'?'selected':'' }}>Insidental</option>
                    <option value="penutupan_buku" {{ request('jenis_opname')=='penutupan_buku'?'selected':'' }}>Penutupan Buku</option>
                </select>
                <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Dari tanggal">
                <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Sampai tanggal">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'status', 'jenis_opname', 'from_date', 'to_date']))
                <a href="{{ route('stock-opname.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Opname</th>
                    <th>Unit</th>
                    <th>Tanggal</th>
                    <th>Jenis Opname</th>
                    <th>Total Item</th>
                    <th>Selisih</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opnames as $o)
                <tr>
                    <td>{{ $opnames->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $o->kode_opname }}</span></td>
                    <td>{{ $o->unit->nama_unit ?? '-' }}</td>
                    <td>{{ $o->tanggal_opname ? \Carbon\Carbon::parse($o->tanggal_opname)->format('d/m/Y') : '-' }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $o->jenis_opname)) }}</span></td>
                    <td>{{ $o->total_item_checked ?? '-' }}</td>
                    <td>
                        @if($o->item_selisih > 0)
                            <span class="text-danger">{{ $o->item_selisih }} item</span>
                        @else
                            <span class="text-success">0</span>
                        @endif
                    </td>
                    <td>
                        @if($o->status == 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @elseif($o->status == 'dibatalkan')
                            <span class="badge badge-danger">Dibatalkan</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($o->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('stock-opname.show', array_merge([$o->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('stock-opname.edit', $o->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-form-1" action="{{ route('stock-opname.destroy', $o->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm('delete-form-1')" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada data stock opname</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $opnames->withQueryString()->links() }}
    </div>
</div>
@endsection

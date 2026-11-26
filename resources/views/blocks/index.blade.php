@extends('adminlte::master')
@section('title', 'Blok & Kamar')
@section('header_actions')
<a href="{{ route('reports.blocks', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a>
<a href="{{ route('blocks.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i> Tambah Blok</a>
@endsection
@section('content')
<div class="row mb-4 dash-row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-th-large"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ $blocks->total() }}</div>
                    <div class="stat-label">Total Blok</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-door-open"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($totalRooms) }}</div>
                    <div class="stat-label">Total Kamar</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-th-large mr-2"></i>Daftar Blok</h5>
        <form action="{{ route('blocks.index') }}" method="GET" class="form-inline">
            <select name="unit_id" class="form-control form-control-sm mr-2" style="width:auto;">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ request('unit_id')==$u->id?'selected':'' }}>{{ $u->nama_unit }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            @if(request()->hasAny(['unit_id']))
            <a href="{{ route('blocks.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Kode</th><th>Nama Blok</th><th>Jenis</th><th>Kapasitas</th><th>Terisi</th><th>Status</th><th width="100">Aksi</th></tr></thead>
                <tbody>
                @forelse($blocks as $block)
                <tr>
                    <td><span class="badge badge-success" style="font-family:monospace;">{{ $block->kode_blok }}</span></td>
                    <td style="font-weight:600;">{{ $block->nama_blok }}</td>
                    <td><span class="badge badge-info">{{ ucfirst($block->jenis_blok) }}</span></td>
                    <td>{{ $block->kapasitas }}</td>
                    <td>{{ $block->terisi }}</td>
                    <td><span class="badge badge-{{ $block->status=='aktif'?'success':'secondary' }}">{{ ucfirst($block->status) }}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('blocks.show', $block) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('blocks.edit', $block) }}" class="btn btn-outline-success"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4"><p style="color:#94a3b8;">Belum ada blok</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($blocks->hasPages())<div class="card-footer">{{ $blocks->withQueryString()->links() }}</div>@endif
</div>
@endsection

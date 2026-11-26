@extends('adminlte::page')

@section('title', 'Sertifikasi & Komplians')

@section('content_header')
    <h1><i class="fas fa-certificate"></i> Sertifikasi & Komplians</h1>
@endsection

@section('header_actions')
    <a href="{{ route('compliance.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Catatan
    </a>
    <a href="{{ route('reports.compliance', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="card dash-row">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-certificate mr-2"></i>Daftar Catatan Komplians</h5>
        <div class="card-tools">
            <form action="{{ route('compliance.index') }}" method="GET" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="perlu_perbaikan" {{ request('status')=='perlu_perbaikan'?'selected':'' }}>Perlu Perbaikan</option>
                    <option value="kedaluwarsa" {{ request('status')=='kedaluwarsa'?'selected':'' }}>Kedaluwarsa</option>
                    <option value="dibatalkan" {{ request('status')=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
                </select>
                <select name="tipe" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Tipe</option>
                    <option value="food_safety" {{ request('tipe')=='food_safety'?'selected':'' }}>Food Safety</option>
                    <option value="sertifikasi_produk" {{ request('tipe')=='sertifikasi_produk'?'selected':'' }}>Sertifikasi Produk</option>
                    <option value="lingkungan" {{ request('tipe')=='lingkungan'?'selected':'' }}>Lingkungan</option>
                    <option value="keamanan_kerja" {{ request('tipe')=='keamanan_kerja'?'selected':'' }}>Keamanan Kerja</option>
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
                <a href="{{ route('compliance.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
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
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Tanggal Pemeriksaan</th>
                    <th>Skor</th>
                    <th>Risiko</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr>
                    <td>{{ $records->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $r->kode_komplians }}</span></td>
                    <td>{{ $r->judul }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst($r->tipe) }}</span></td>
                    <td>{{ $r->tanggal_pemeriksaan ? \Carbon\Carbon::parse($r->tanggal_pemeriksaan)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $r->nilai_skor ?? '-' }}</td>
                    <td>
                        @if($r->tingkat_risiko == 'tinggi')
                            <span class="badge badge-danger">Tinggi</span>
                        @elseif($r->tingkat_risiko == 'sedang')
                            <span class="badge badge-warning">Sedang</span>
                        @else
                            <span class="badge badge-success">{{ ucfirst($r->tingkat_risiko ?? '-') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($r->status == 'aktif')
                            <span class="badge badge-success">Aktif</span>
                        @elseif($r->status == 'kedaluwarsa')
                            <span class="badge badge-danger">Kedaluwarsa</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($r->status) }}</span>
                        @endif
                        @if($r->status_histories_count > 0)
                            <a href="{{ route('compliance.show', array_merge([$r->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="badge badge-light border ml-1" title="{{ $r->status_histories_count }}x perubahan status" style="font-size:11px;"><i class="fas fa-history"></i> {{ $r->status_histories_count }}</a>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('compliance.show', array_merge([$r->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('compliance.edit', $r->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-form-1" action="{{ route('compliance.destroy', $r->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm('delete-form-1')" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada data komplians</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $records->withQueryString()->links() }}
    </div>
</div>
@endsection

@extends('adminlte::page')

@section('title', 'Pengolahan Hasil')

@section('content_header')
    <h1><i class="fas fa-industry"></i> Pengolahan Hasil</h1>
@endsection

@section('header_actions')
    <a href="{{ route('processing.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Batch
    </a>
    <a href="{{ route('reports.processing', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="card dash-row">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-industry mr-2"></i>Daftar Batch Pengolahan</h5>
        <div class="card-tools">
            <form action="{{ route('processing.index') }}" method="GET" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="proses" {{ request('status')=='proses'?'selected':'' }}>Proses</option>
                    <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
                    <option value="gagal" {{ request('status')=='gagal'?'selected':'' }}>Gagal</option>
                </select>
                <select name="jenis_pengolahan" class="form-control form-control-sm mr-2" style="width:auto;">
                    <option value="">Semua Jenis Pengolahan</option>
                    <option value="penggilingan" {{ request('jenis_pengolahan')=='penggilingan'?'selected':'' }}>Penggilingan</option>
                    <option value="fermentasi" {{ request('jenis_pengolahan')=='fermentasi'?'selected':'' }}>Fermentasi</option>
                    <option value="pengolahan_umum" {{ request('jenis_pengolahan')=='pengolahan_umum'?'selected':'' }}>Pengolahan Umum</option>
                    <option value="pengeringan" {{ request('jenis_pengolahan')=='pengeringan'?'selected':'' }}>Pengeringan</option>
                    <option value="pengepakan" {{ request('jenis_pengolahan')=='pengepakan'?'selected':'' }}>Pengepakan</option>
                </select>
                <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Dari tanggal">
                <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Sampai tanggal">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'status', 'jenis_pengolahan', 'from_date', 'to_date']))
                <a href="{{ route('processing.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Batch</th>
                    <th>Nama Produk</th>
                    <th>Jenis Pengolahan</th>
                    <th>Tanggal Produksi</th>
                    <th>Jumlah Produksi</th>
                    <th>Biaya</th>
                    <th>Harga Jual</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $b)
                <tr>
                    <td>{{ $batches->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-info">{{ $b->kode_batch }}</span></td>
                    <td>{{ $b->nama_produk }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst($b->jenis_pengolahan) }}</span></td>
                    <td>{{ $b->tanggal_produksi ? \Carbon\Carbon::parse($b->tanggal_produksi)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $b->jumlah_produksi }} {{ $b->satuan_output }}</td>
                    <td>Rp {{ number_format($b->biaya_produksi ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($b->harga_jual_satuan ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @if($b->status == 'proses')
                            <span class="badge badge-warning">Diproses</span>
                        @elseif($b->status == 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @elseif($b->status == 'gagal')
                            <span class="badge badge-danger">Gagal</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($b->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('processing.show', array_merge([$b->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('processing.edit', $b->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                        <form id="delete-form-1" action="{{ route('processing.destroy', $b->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm('delete-form-1')" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">Belum ada data batch pengolahan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $batches->withQueryString()->links() }}
    </div>
</div>
@endsection

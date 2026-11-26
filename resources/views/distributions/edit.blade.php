@extends('adminlte::page')
@section('title', 'Edit Distribusi')
@section('header_actions')
    <a href="{{ route('distributions.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('distributions.update', $distribution) }}" method="POST">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-md-4"><div class="form-group"><label>Nama Penerima</label><input type="text" name="penerima_nama" class="form-control" value="{{ old('penerima_nama', $distribution->penerima_nama) }}"></div></div>
            <div class="col-md-4"><div class="form-group"><label>Tipe Distribusi</label><select name="tipe_distribusi" class="form-control">
                @foreach(['konsumsi_wbp','penjualan_lokal','penjualan_eksternal','donasi','konversi_produksi'] as $t)<option value="{{ $t }}" {{ $distribution->tipe_distribusi===$t?'selected':'' }}>{{ str_replace('_',' ',ucfirst($t)) }}</option>@endforeach
            </select></div></div>
            <div class="col-md-4"><div class="form-group"><label>Tanggal</label><input type="date" name="tanggal_distribusi" class="form-control" value="{{ $distribution->tanggal_distribusi->format('Y-m-d') }}"></div></div>
        </div>
        <div class="row">
            <div class="col-md-4"><div class="form-group"><label>Total Nilai</label><input type="number" name="total_nilai" class="form-control" value="{{ $distribution->total_nilai }}" step="0.01"></div></div>
            <div class="col-md-4"><div class="form-group"><label>Status</label><select name="status" class="form-control">
                @foreach(['draft','dikirim','diterima','dibatalkan'] as $s)<option value="{{ $s }}" {{ $distribution->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
            </select></div></div>
        </div>
        <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ $distribution->keterangan }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
    </form>
</div></div>
@stop

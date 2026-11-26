@extends('adminlte::page')
@section('title', 'Tambah Kandang')
@section('header_actions')
    <a href="{{ route('livestock-barns.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('livestock-barns.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-3"><div class="form-group"><label>Kode Kandang *</label><input type="text" name="kode_kandang" class="form-control" value="{{ old('kode_kandang') }}" required></div></div>
            <div class="col-md-5"><div class="form-group"><label>Nama Kandang *</label><input type="text" name="nama_kandang" class="form-control" value="{{ old('nama_kandang') }}" required></div></div>
            <div class="col-md-4"><div class="form-group"><label>Jenis Ternak *</label><input type="text" name="jenis_ternak" class="form-control" value="{{ old('jenis_ternak') }}" placeholder="Ayam, Sapi, Kambing..." required></div></div>
        </div>
        <div class="row">
            <div class="col-md-3"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
            <div class="col-md-2"><div class="form-group"><label>Kapasitas *</label><input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', 0) }}" min="0" required></div></div>
            <div class="col-md-2"><div class="form-group"><label>Luas (m²)</label><input type="number" name="luas_m2" class="form-control" value="{{ old('luas_m2') }}" step="0.01"></div></div>
            <div class="col-md-2"><div class="form-group"><label>Tipe Kandang</label><input type="text" name="tipe_kandang" class="form-control" value="{{ old('tipe_kandang') }}" placeholder="Closed, Open..."></div></div>
            <div class="col-md-3"><div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="aktif">Aktif</option><option value="renovasi">Renovasi</option><option value="nonaktif">Nonaktif</option></select></div></div>
        </div>
        <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@stop

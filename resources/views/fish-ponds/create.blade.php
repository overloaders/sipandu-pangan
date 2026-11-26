@extends('adminlte::page')
@section('title', 'Tambah Kolam Ikan')
@section('header_actions')
    <a href="{{ route('fish-ponds.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('fish-ponds.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-3"><div class="form-group"><label>Kode Kolam *</label><input type="text" name="kode_kolam" class="form-control" value="{{ old('kode_kolam') }}" required></div></div>
            <div class="col-md-5"><div class="form-group"><label>Nama Kolam *</label><input type="text" name="nama_kolam" class="form-control" value="{{ old('nama_kolam') }}" required></div></div>
            <div class="col-md-4"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
        </div>
        <div class="row">
            <div class="col-md-2"><div class="form-group"><label>Luas (m²) *</label><input type="number" name="luas_m2" class="form-control" value="{{ old('luas_m2') }}" step="0.01" min="0.01" required></div></div>
            <div class="col-md-2"><div class="form-group"><label>Kedalaman (m)</label><input type="number" name="kedalaman_m" class="form-control" value="{{ old('kedalaman_m') }}" step="0.01"></div></div>
            <div class="col-md-2"><div class="form-group"><label>Volume (m³)</label><input type="number" name="volume_m3" class="form-control" value="{{ old('volume_m3') }}" step="0.01"></div></div>
            <div class="col-md-3"><div class="form-group"><label>Tipe Kolam</label><input type="text" name="tipe_kolam" class="form-control" value="{{ old('tipe_kolam') }}" placeholder="Tanah, Beton, Terpal..."></div></div>
            <div class="col-md-3"><div class="form-group"><label>Sumber Air</label><input type="text" name="sumber_air" class="form-control" value="{{ old('sumber_air') }}" placeholder="Sungai, Sumur..."></div></div>
        </div>
        <div class="row">
            <div class="col-md-6"><div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div></div>
            <div class="col-md-6"><div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="aktif">Aktif</option><option value="kering">Kering</option><option value="renovasi">Renovasi</option><option value="nonaktif">Nonaktif</option></select></div></div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@stop

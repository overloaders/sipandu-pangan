@extends('adminlte::page')
@section('title', 'Tambah Rencana Kerja')
@section('header_actions')
    <a href="{{ route('farming-plans.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('farming-plans.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-3"><div class="form-group"><label>Kode Rencana *</label><input type="text" name="kode_rencana" class="form-control" value="{{ old('kode_rencana', 'RP-'.date('Ymd')) }}" required></div></div>
            <div class="col-md-3"><div class="form-group"><label>Lahan *</label><select name="land_plot_id" class="form-control" required><option value="">-- Pilih --</option>@foreach($landPlots as $l)<option value="{{ $l->id }}">{{ $l->nama_lahan }}</option>@endforeach</select></div></div>
            <div class="col-md-3"><div class="form-group"><label>Jenis Tanaman *</label><select name="crop_type_id" class="form-control" required><option value="">-- Pilih --</option>@foreach($cropTypes as $c)<option value="{{ $c->id }}">{{ $c->nama_tanaman }}</option>@endforeach</select></div></div>
            <div class="col-md-3"><div class="form-group"><label>Program Pangan</label><select name="food_program_id" class="form-control"><option value="">-- Pilih --</option>@foreach($foodPrograms as $fp)<option value="{{ $fp->id }}">{{ $fp->nama_program }}</option>@endforeach</select></div></div>
        </div>
        <div class="row">
            <div class="col-md-2"><div class="form-group"><label>Jml Tanaman *</label><input type="number" name="jumlah_tanaman" class="form-control" value="{{ old('jumlah_tanaman', 1) }}" min="1" required></div></div>
            <div class="col-md-2"><div class="form-group"><label>Luas (m²)</label><input type="number" name="luas_tanam_m2" class="form-control" value="{{ old('luas_tanam_m2') }}" step="0.01"></div></div>
            <div class="col-md-2"><div class="form-group"><label>Tgl Tanam Rencana *</label><input type="date" name="tanggal_tanam_rencana" class="form-control" value="{{ old('tanggal_tanam_rencana') }}" required></div></div>
            <div class="col-md-2"><div class="form-group"><label>Tgl Panen Rencana</label><input type="date" name="tanggal_panen_rencana" class="form-control" value="{{ old('tanggal_panen_rencana') }}"></div></div>
            <div class="col-md-2"><div class="form-group"><label>Estimasi Hasil (kg)</label><input type="number" name="estimasi_hasil_kg" class="form-control" value="{{ old('estimasi_hasil_kg') }}" step="0.01"></div></div>
            <div class="col-md-2"><div class="form-group"><label>Estimasi Pendapatan</label><input type="number" name="estimasi_pendapatan" class="form-control" value="{{ old('estimasi_pendapatan') }}"></div></div>
        </div>
        <div class="form-group"><label>Metode Budi Daya</label><input type="text" name="metode_budi_daya" class="form-control" value="{{ old('metode_budi_daya') }}" placeholder="Contoh: Organik, Hidroponik, Konvensional"></div>
        <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </form>
</div></div>
@stop

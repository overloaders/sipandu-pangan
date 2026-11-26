@extends('adminlte::master')
@section('title', 'Tambah Program')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-seedling mr-2"></i>Form Program Ketahanan Pangan</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('food-programs.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Kode Program *</label><input type="text" name="kode_program" class="form-control" value="{{ old('kode_program') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Jenis Program *</label><select name="jenis_program" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option><option value="pertanian" {{ old('jenis_program')=='pertanian'?'selected':'' }}>Pertanian</option><option value="peternakan" {{ old('jenis_program')=='peternakan'?'selected':'' }}>Peternakan</option><option value="perikanan" {{ old('jenis_program')=='perikanan'?'selected':'' }}>Perikanan</option><option value="integrated" {{ old('jenis_program')=='integrated'?'selected':'' }}>Integrated</option><option value="pengolahan" {{ old('jenis_program')=='pengolahan'?'selected':'' }}>Pengolahan</option></select></div></div>
                    </div>
                    <div class="form-group"><label>Nama Program *</label><input type="text" name="nama_program" class="form-control" value="{{ old('nama_program') }}" required style="border-radius:8px;"></div>
                    <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" style="border-radius:8px;">{{ old('deskripsi') }}</textarea></div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Tanggal Mulai *</label><input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Rencana Selesai</label><input type="date" name="tanggal_rencana_selesai" class="form-control" value="{{ old('tanggal_rencana_selesai') }}" style="border-radius:8px;"></div></div>
                    </div>
                    <div class="form-group"><label>Anggaran (Rp)</label><input type="number" name="anggaran" class="form-control" value="{{ old('anggaran', 0) }}" min="0" step="0.01" style="border-radius:8px;"></div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('food-programs.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

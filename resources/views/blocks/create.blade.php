@extends('adminlte::master')
@section('title', 'Tambah Blok')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-th-large mr-2"></i>Form Blok</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('blocks.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Kode Blok *</label><input type="text" name="kode_blok" class="form-control" value="{{ old('kode_blok') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nama Blok *</label><input type="text" name="nama_blok" class="form-control" value="{{ old('nama_blok') }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Jenis Blok *</label><select name="jenis_blok" class="form-control" required style="border-radius:8px;"><option value="umum">Umum</option><option value="khusus">Khusus</option><option value="isolasi">Isolasi</option><option value="maximum">Maximum</option><option value="terapi">Terapi</option><option value="wanita">Wanita</option><option value="anak">Anak</option></select></div></div>
                    </div>
                    <div class="form-group"><label>Kapasitas *</label><input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', 0) }}" min="0" required style="border-radius:8px;"></div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('blocks.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

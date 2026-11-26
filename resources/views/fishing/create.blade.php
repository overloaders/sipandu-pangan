@extends('adminlte::master')
@section('title', 'Tambah Kolam')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-water mr-2"></i>Form Kolam Ikan</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('fish-ponds.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Kode Kolam *</label><input type="text" name="kode_kolam" class="form-control" value="{{ old('kode_kolam') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nama Kolam *</label><input type="text" name="nama_kolam" class="form-control" value="{{ old('nama_kolam') }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Program Pangan</label><select name="food_program_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih (opsional) --</option>@foreach($foodPrograms as $fp)<option value="{{ $fp->id }}" {{ old('food_program_id')==$fp->id?'selected':'' }}>{{ $fp->nama_program }}</option>@endforeach</select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Luas (m²) *</label><input type="number" name="luas_m2" class="form-control" value="{{ old('luas_m2') }}" step="0.01" min="0" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Tipe Kolam *</label><select name="tipe_kolam" class="form-control" required style="border-radius:8px;"><option value="tanah">Tanah</option><option value="semen">Semen</option><option value="terpal">Terpal</option><option value="beton">Beton</option><option value="floating_cage">Floating Cage</option></select></div></div>
                    </div>
                    <div class="form-group"><label>Sumber Air *</label><select name="sumber_air" class="form-control" required style="border-radius:8px;"><option value="sungai">Sungai</option><option value="sumur">Sumur</option><option value="hujan">Hujan</option><option value="pompa">Pompa</option><option value="PDAM">PDAM</option></select></div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('fish-ponds.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('adminlte::master')
@section('title', 'Tambah Kandang')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-warehouse mr-2"></i>Form Kandang</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('livestock-barns.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Kode Kandang *</label><input type="text" name="kode_kandang" class="form-control" value="{{ old('kode_kandang') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nama Kandang *</label><input type="text" name="nama_kandang" class="form-control" value="{{ old('nama_kandang') }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Program Pangan</label><select name="food_program_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih (opsional) --</option>@foreach($foodPrograms as $fp)<option value="{{ $fp->id }}" {{ old('food_program_id')==$fp->id?'selected':'' }}>{{ $fp->nama_program }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Jenis Ternak *</label><select name="jenis_ternak" class="form-control" required style="border-radius:8px;"><option value="ayam">Ayam</option><option value="bebek">Bebek</option><option value="kambing">Kambing</option><option value="sapi">Sapi</option><option value="babi">Babi</option><option value="kelinci">Kelinci</option><option value="kambing_perah">Kambing Perah</option><option value="sapi_perah">Sapi Perah</option><option value="lainnya">Lainnya</option></select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Kapasitas *</label><input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas') }}" min="1" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Luas (m²) *</label><input type="number" name="luas_m2" class="form-control" value="{{ old('luas_m2') }}" step="0.01" min="0" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Tipe *</label><select name="tipe_kandang" class="form-control" required style="border-radius:8px;"><option value="kandang_baterai">Kandang Baterai</option><option value="kandang_umbaran">Kandang Umbara</option><option value="kandang_koloni">Kandang Koloni</option><option value="semi_ekstensif">Semi Ekstensif</option></select></div></div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('livestock-barns.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

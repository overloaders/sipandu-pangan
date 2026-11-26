@extends('adminlte::master')
@section('title', 'Registrasi WBP Baru')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-user-plus mr-2"></i>Form Registrasi WBP</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('wbp.store') }}">
                    @csrf
                    <h6 style="font-weight:700;color:#10b981;border-bottom:2px solid #10b981;padding-bottom:6px;"><i class="fas fa-id-card mr-1"></i> Data Identitas</h6>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Nomor Induk *</label><input type="text" name="nomor_induk" class="form-control" value="{{ old('nomor_induk') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>NIK *</label><input type="text" name="nik" class="form-control" value="{{ old('nik') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Nama Lengkap *</label><input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Jenis Kelamin *</label><select name="jenis_kelamin" class="form-control" required style="border-radius:8px;"><option value="L" {{ old('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option><option value="P" {{ old('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option></select></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Tanggal Lahir *</label><input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Tempat Lahir *</label><input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Agama *</label><select name="agama" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select></div></div>
                    </div>

                    <h6 class="mt-3" style="font-weight:700;color:#3b82f6;border-bottom:2px solid #3b82f6;padding-bottom:6px;"><i class="fas fa-gavel mr-1"></i> Data Hukuman</h6>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Tanggal Masuk *</label><input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Masa Hukuman (hari) *</label><input type="number" name="masa_hukuman_hari" class="form-control" value="{{ old('masa_hukuman_hari') }}" min="1" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih Unit --</option>@foreach($units as $u)<option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Blok</label><select name="block_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih Blok --</option>@foreach($blocks as $b)<option value="{{ $b->id }}" {{ old('block_id')==$b->id?'selected':'' }}>{{ $b->kode_blok }} - {{ $b->nama_blok }}</option>@endforeach</select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Kamar</label><select name="room_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih Kamar --</option>@foreach($rooms as $r)<option value="{{ $r->id }}" {{ old('room_id')==$r->id?'selected':'' }}>{{ $r->kode_kamar }} - {{ $r->nama_kamar }}</option>@endforeach</select></div></div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <a href="{{ route('wbp.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('adminlte::page')
@section('title', 'Tambah Unit')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-building mr-2"></i>Form Tambah Unit</h5>
        <div class="card-tools"><a href="{{ route('master.units.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.units.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Kode Unit *</label><input type="text" name="kode_unit" class="form-control" value="{{ old('kode_unit') }}" required placeholder="contoh: UPR-001"></div>
                    <div class="form-group"><label>Nama Unit *</label><input type="text" name="nama_unit" class="form-control" value="{{ old('nama_unit') }}" required></div>
                    <div class="form-group"><label>Tipe Unit *</label>
                        <select name="tipe_unit" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['lapas', 'lapas_anak', 'rutan', 'cabang_rutan', 'lp', 'lp_anak', 'lpk', 'psms'] as $t)
                            <option value="{{ $t }}" {{ old('tipe_unit')==$t?'selected' : '' }}>{{ strtoupper(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Unit Induk</label>
                        <select name="parent_id" class="form-control">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($parentUnits as $p)
                            <option value="{{ $p->id }}" {{ old('parent_id')==$p->id?'selected':'' }}>{{ $p->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Provinsi</label><input type="text" name="provinsi" class="form-control" value="{{ old('provinsi') }}"></div>
                    <div class="form-group"><label>Kota/Kabupaten</label><input type="text" name="kota_kab" class="form-control" value="{{ old('kota_kab') }}"></div>
                    <div class="form-group"><label>Alamat</label><textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea></div>
                    <div class="form-group"><label>Telepon</label><input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
                    <div class="form-group">
                        <label>Logo (untuk kop surat laporan)</label>
                        <input type="file" name="logo" class="form-control-file" accept="image/png,image/jpeg,image/svg+xml">
                        <small class="form-text text-muted">PNG/JPG/SVG, maks 2MB. Jika kosong, laporan tanpa logo.</small>
                    </div>
                    <div class="form-group"><label>Kapasitas WBP</label><input type="number" name="kapasitas_wbp" class="form-control" value="{{ old('kapasitas_wbp') }}"></div>
                    <div class="form-group"><label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" {{ old('status','aktif')=='aktif'?'selected':'' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status')=='nonaktif'?'selected':'' }}>Non-aktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('master.units.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
        </div>
    </form>
</div>
@endsection

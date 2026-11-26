@extends('adminlte::page')
@section('title', 'Tambah Jenis Tanaman')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-seedling mr-2"></i>Form Tambah Jenis Tanaman</h5>
        <div class="card-tools"><a href="{{ route('master.crop-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.crop-types.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Kode Tanaman *</label><input type="text" name="kode_tanaman" class="form-control" value="{{ old('kode_tanaman') }}" required placeholder="contoh: TNM-001"></div>
                    <div class="form-group"><label>Nama Tanaman *</label><input type="text" name="nama_tanaman" class="form-control" value="{{ old('nama_tanaman') }}" required placeholder="contoh: Padi"></div>
                    <div class="form-group"><label>Nama Latin</label><input type="text" name="nama_latin" class="form-control" value="{{ old('nama_latin') }}" placeholder="contoh: Oryza sativa"></div>
                    <div class="form-group"><label>Kategori *</label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="pangan" {{ old('kategori')=='pangan'?'selected':'' }}>Pangan</option>
                            <option value="sayuran" {{ old('kategori')=='sayuran'?'selected':'' }}>Sayuran</option>
                            <option value="buah" {{ old('kategori')=='buah'?'selected':'' }}>Buah</option>
                            <option value="rempah" {{ old('kategori')=='rempah'?'selected':'' }}>Rempah</option>
                            <option value="tanaman_hias" {{ old('kategori')=='tanaman_hias'?'selected':'' }}>Tanaman Hias</option>
                            <option value="lainnya" {{ old('kategori')=='lainnya'?'selected':'' }}>Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Masa Tanam (hari)</label><input type="number" name="masa_tanam_hari" class="form-control" value="{{ old('masa_tanam_hari') }}"></div>
                    <div class="form-group"><label>Hasil per m² (kg)</label><input type="number" name="hasil_per_m2_kg" class="form-control" step="0.01" value="{{ old('hasil_per_m2_kg') }}"></div>
                    <div class="form-group"><label>Harga Jual per kg (Rp)</label><input type="number" name="harga_jual_per_kg" class="form-control" step="0.01" value="{{ old('harga_jual_per_kg') }}"></div>
                    <div class="form-group"><label>Musim Tanam</label><input type="text" name="musim_tanam" class="form-control" value="{{ old('musim_tanam') }}" placeholder="contoh: Musim Hujan, Sepanjang Tahun"></div>
                </div>
            </div>
            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('master.crop-types.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
        </div>
    </form>
</div>
@endsection

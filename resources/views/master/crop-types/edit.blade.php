@extends('adminlte::page')
@section('title', 'Edit Jenis Tanaman')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-seedling mr-2"></i>{{ $cropType->nama_tanaman }}</h5>
        <div class="card-tools"><a href="{{ route('master.crop-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.crop-types.update', $cropType) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nama Tanaman *</label><input type="text" name="nama_tanaman" class="form-control" value="{{ old('nama_tanaman', $cropType->nama_tanaman) }}" required></div>
                    <div class="form-group"><label>Nama Latin</label><input type="text" name="nama_latin" class="form-control" value="{{ old('nama_latin', $cropType->nama_latin) }}"></div>
                    <div class="form-group"><label>Kategori *</label>
                        <select name="kategori" class="form-control" required>
                            @foreach(['pangan', 'sayuran', 'buah', 'rempah', 'tanaman_hias', 'lainnya'] as $k)
                            <option value="{{ $k }}" {{ old('kategori', $cropType->kategori)==$k?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$k)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Masa Tanam (hari)</label><input type="number" name="masa_tanam_hari" class="form-control" value="{{ old('masa_tanam_hari', $cropType->masa_tanam_hari) }}"></div>
                    <div class="form-group"><label>Hasil per m² (kg)</label><input type="number" name="hasil_per_m2_kg" class="form-control" step="0.01" value="{{ old('hasil_per_m2_kg', $cropType->hasil_per_m2_kg) }}"></div>
                    <div class="form-group"><label>Harga Jual per kg (Rp)</label><input type="number" name="harga_jual_per_kg" class="form-control" step="0.01" value="{{ old('harga_jual_per_kg', $cropType->harga_jual_per_kg) }}"></div>
                    <div class="form-group"><label>Musim Tanam</label><input type="text" name="musim_tanam" class="form-control" value="{{ old('musim_tanam', $cropType->musim_tanam) }}"></div>
                </div>
            </div>
            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $cropType->keterangan) }}</textarea></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('master.crop-types.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
        </div>
    </form>
</div>
@endsection

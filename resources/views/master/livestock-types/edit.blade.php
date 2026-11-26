@extends('adminlte::page')
@section('title', 'Edit Jenis Ternak')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-paw mr-2"></i>{{ $livestockType->nama_jenis }}</h5>
        <div class="card-tools"><a href="{{ route('master.livestock-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.livestock-types.update', $livestockType) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nama Jenis *</label><input type="text" name="nama_jenis" class="form-control" value="{{ old('nama_jenis', $livestockType->nama_jenis) }}" required></div>
                    <div class="form-group"><label>Kategori *</label>
                        <select name="kategori" class="form-control" required>
                            @foreach(['unggas', 'ruminansia', 'unggas_air', 'ikan', 'lainnya'] as $k)
                            <option value="{{ $k }}" {{ old('kategori', $livestockType->kategori)==$k?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$k)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Berat Rata-rata (kg)</label><input type="number" name="berat_rata_kg" class="form-control" step="0.1" value="{{ old('berat_rata_kg', $livestockType->berat_rata_kg) }}"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Harga Beli per Ekor (Rp)</label><input type="number" name="harga_beli_per_ekor" class="form-control" step="0.01" value="{{ old('harga_beli_per_ekor', $livestockType->harga_beli_per_ekor) }}"></div>
                    <div class="form-group"><label>Harga Jual per kg (Rp)</label><input type="number" name="harga_jual_per_kg" class="form-control" step="0.01" value="{{ old('harga_jual_per_kg', $livestockType->harga_jual_per_kg) }}"></div>
                    <div class="form-group"><label>Masa Pemeliharaan (hari)</label><input type="number" name="masa_pemeliharaan_hari" class="form-control" value="{{ old('masa_pemeliharaan_hari', $livestockType->masa_pemeliharaan_hari) }}"></div>
                    <div class="form-group"><label>Pakan Utama</label><input type="text" name="pakan_utama" class="form-control" value="{{ old('pakan_utama', $livestockType->pakan_utama) }}"></div>
                </div>
            </div>
            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $livestockType->keterangan) }}</textarea></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('master.livestock-types.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
        </div>
    </form>
</div>
@endsection

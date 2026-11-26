@extends('adminlte::page')
@section('title', 'Tambah Jenis Ternak')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-paw mr-2"></i>Form Tambah Jenis Ternak</h5>
        <div class="card-tools"><a href="{{ route('master.livestock-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.livestock-types.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Kode Jenis *</label><input type="text" name="kode_jenis" class="form-control" value="{{ old('kode_jenis') }}" required placeholder="contoh: TRN-001"></div>
                    <div class="form-group"><label>Nama Jenis *</label><input type="text" name="nama_jenis" class="form-control" value="{{ old('nama_jenis') }}" required placeholder="contoh: Ayam Kampung"></div>
                    <div class="form-group"><label>Kategori *</label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="unggas" {{ old('kategori')=='unggas'?'selected':'' }}>Unggas</option>
                            <option value="ruminansia" {{ old('kategori')=='ruminansia'?'selected':'' }}>Ruminansia</option>
                            <option value="unggas_air" {{ old('kategori')=='unggas_air'?'selected':'' }}>Unggas Air</option>
                            <option value="ikan" {{ old('kategori')=='ikan'?'selected':'' }}>Ikan</option>
                            <option value="lainnya" {{ old('kategori')=='lainnya'?'selected':'' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Berat Rata-rata (kg)</label><input type="number" name="berat_rata_kg" class="form-control" step="0.1" value="{{ old('berat_rata_kg') }}"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Harga Beli per Ekor (Rp)</label><input type="number" name="harga_beli_per_ekor" class="form-control" step="0.01" value="{{ old('harga_beli_per_ekor') }}"></div>
                    <div class="form-group"><label>Harga Jual per kg (Rp)</label><input type="number" name="harga_jual_per_kg" class="form-control" step="0.01" value="{{ old('harga_jual_per_kg') }}"></div>
                    <div class="form-group"><label>Masa Pemeliharaan (hari)</label><input type="number" name="masa_pemeliharaan_hari" class="form-control" value="{{ old('masa_pemeliharaan_hari') }}"></div>
                    <div class="form-group"><label>Pakan Utama</label><input type="text" name="pakan_utama" class="form-control" value="{{ old('pakan_utama') }}" placeholder="contoh: Pelet, Dedak"></div>
                </div>
            </div>
            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('master.livestock-types.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
        </div>
    </form>
</div>
@endsection

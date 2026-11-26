@extends('adminlte::page')
@section('title', 'Tambah Kategori Inventaris')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-tags mr-2"></i>Form Tambah Kategori Inventaris</h5>
        <div class="card-tools"><a href="{{ route('master.inventory-categories.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.inventory-categories.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Kode Kategori *</label><input type="text" name="kode_kategori" class="form-control" value="{{ old('kode_kategori') }}" required placeholder="contoh: KAT-001"></div>
                    <div class="form-group"><label>Nama Kategori *</label><input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori') }}" required placeholder="contoh: Pakan Ternak"></div>
                    <div class="form-group"><label>Tipe *</label>
                        <select name="tipe" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="pangan" {{ old('tipe')=='pangan'?'selected':'' }}>Pangan</option>
                            <option value="non_pangan" {{ old('tipe')=='non_pangan'?'selected':'' }}>Non-Pangan</option>
                            <option value="obat" {{ old('tipe')=='obat'?'selected':'' }}>Obat/Obat Hewan</option>
                            <option value="pakan" {{ old('tipe')=='pakan'?'selected':'' }}>Pakan Ternak</option>
                            <option value="bahan_bakar" {{ old('tipe')=='bahan_bakar'?'selected':'' }}>Bahan Bakar</option>
                            <option value="alat" {{ old('tipe')=='alat'?'selected':'' }}>Alat/Peralatan</option>
                            <option value="lainnya" {{ old('tipe')=='lainnya'?'selected':'' }}>Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Satuan Standar</label><input type="text" name="satuan_standar" class="form-control" value="{{ old('satuan_standar') }}" placeholder="contoh: kg, liter, pcs"></div>
                    <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea></div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('master.inventory-categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
        </div>
    </form>
</div>
@endsection

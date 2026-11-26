@extends('adminlte::page')
@section('title', 'Edit Kategori Inventaris')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-tags mr-2"></i>{{ $inventoryCategory->nama_kategori }}</h5>
        <div class="card-tools"><a href="{{ route('master.inventory-categories.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.inventory-categories.update', $inventoryCategory) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nama Kategori *</label><input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori', $inventoryCategory->nama_kategori) }}" required></div>
                    <div class="form-group"><label>Tipe *</label>
                        <select name="tipe" class="form-control" required>
                            @foreach(['pangan', 'non_pangan', 'obat', 'pakan', 'bahan_bakar', 'alat', 'lainnya'] as $t)
                            <option value="{{ $t }}" {{ old('tipe', $inventoryCategory->tipe)==$t?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Satuan Standar</label><input type="text" name="satuan_standar" class="form-control" value="{{ old('satuan_standar', $inventoryCategory->satuan_standar) }}"></div>
                    <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $inventoryCategory->keterangan) }}</textarea></div>
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

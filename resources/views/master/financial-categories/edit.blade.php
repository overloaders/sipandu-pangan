@extends('adminlte::page')
@section('title', 'Edit Kategori Keuangan')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-wallet mr-2"></i>{{ $financialCategory->nama_kategori }}</h5>
        <div class="card-tools"><a href="{{ route('master.financial-categories.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
    </div>
    <form action="{{ route('master.financial-categories.update', $financialCategory) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nama Kategori *</label><input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori', $financialCategory->nama_kategori) }}" required></div>
                    <div class="form-group"><label>Tipe *</label>
                        <select name="tipe" class="form-control" required>
                            <option value="pemasukan" {{ old('tipe', $financialCategory->tipe)=='pemasukan'?'selected':'' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ old('tipe', $financialCategory->tipe)=='pengeluaran'?'selected':'' }}>Pengeluaran</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $financialCategory->keterangan) }}</textarea></div>
                    <div class="form-group"><label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ old('is_active', $financialCategory->is_active)?'selected':'' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $financialCategory->is_active)==0?'selected':'' }}>Non-aktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('master.financial-categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
        </div>
    </form>
</div>
@endsection

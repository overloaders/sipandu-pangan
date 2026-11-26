@extends('adminlte::master')
@section('title', 'Edit Item: ' . $item->nama_item)
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Item</h5><a href="/inventory" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                <form method="POST" action="{{ route('inventory.update', $item) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Item</label><input type="text" name="nama_item" class="form-control" value="{{ old('nama_item', $item->nama_item) }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Kategori</label><select name="category_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id',$item->category_id)==$c->id?'selected':'' }}>{{ $c->nama_kategori }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Stok Saat Ini</label><input type="number" name="stok_saat_ini" class="form-control" value="{{ old('stok_saat_ini', $item->stok_saat_ini) }}" min="0" step="0.01" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Stok Minimum</label><input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', $item->stok_minimum) }}" min="0" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Harga Jual (Rp)</label><input type="number" name="harga_jual" class="form-control" value="{{ old('harga_jual', $item->harga_jual) }}" min="0" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Status</label><select name="status" class="form-control" style="border-radius:8px;">@foreach(['tersedia','stok_habis','kadaluarsa','rusak','nonaktif'] as $s)<option value="{{ $s }}" {{ old('status',$item->status)==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></div></div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="/inventory" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

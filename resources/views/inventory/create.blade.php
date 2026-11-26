@extends('adminlte::master')
@section('title', 'Tambah Item')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-box-open mr-2"></i>Form Item Inventaris</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('inventory.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Kode Item *</label><input type="text" name="kode_item" class="form-control" value="{{ old('kode_item') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nama Item *</label><input type="text" name="nama_item" class="form-control" value="{{ old('nama_item') }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Kategori</label><select name="category_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->nama_kategori }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Tipe Pangan *</label><select name="tipe_pangan" class="form-control" required style="border-radius:8px;"><option value="basah">Basah</option><option value="kering">Kering</option><option value="olahan">Olahan</option><option value="bumbu">Bumbu</option><option value="bahan_baku">Bahan Baku</option></select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Asal *</label><select name="asal" class="form-control" required style="border-radius:8px;"><option value="produksi_sendiri">Produksi Sendiri</option><option value="pembelian">Pembelian</option><option value="hibah">Hibah</option><option value="konversi">Konversi</option></select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Satuan</label><input type="text" name="satuan" class="form-control" value="{{ old('satuan', 'kg') }}" style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Stok Awal</label><input type="number" name="stok_saat_ini" class="form-control" value="{{ old('stok_saat_ini', 0) }}" min="0" step="0.01" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Stok Minimum</label><input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', 0) }}" min="0" step="0.01" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Harga Beli (Rp)</label><input type="number" name="harga_beli_rata" class="form-control" value="{{ old('harga_beli_rata', 0) }}" min="0" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Harga Jual (Rp)</label><input type="number" name="harga_jual" class="form-control" value="{{ old('harga_jual', 0) }}" min="0" style="border-radius:8px;"></div></div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

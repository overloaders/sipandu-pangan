@extends('adminlte::master')
@section('title', 'Tambah Transaksi Keuangan')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-wallet mr-2"></i>Form Transaksi Keuangan</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('financial.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Tipe *</label><select name="tipe" class="form-control" required style="border-radius:8px;"><option value="pemasukan">Pemasukan</option><option value="pengeluaran">Pengeluaran</option></select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Sumber Dana *</label><select name="sumber_dana" class="form-control" required style="border-radius:8px;"><option value="anggaran">Anggaran</option><option value="penjualan_pangan">Penjualan Pangan</option><option value="pnbp">PNBP</option><option value="hibah">Hibah</option><option value="sumbangan">Sumbangan</option><option value="lainnya">Lainnya</option></select></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Metode *</label><select name="metode_pembayaran" class="form-control" required style="border-radius:8px;"><option value="tunai">Tunai</option><option value="transfer">Transfer</option><option value="qris">QRIS</option><option value="lainnya">Lainnya</option></select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Kategori</label><select name="category_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->nama_kategori }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Jumlah (Rp) *</label><input type="number" name="jumlah" class="form-control" value="{{ old('jumlah') }}" min="0.01" step="0.01" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Tanggal *</label><input type="date" name="tanggal_transaksi" class="form-control" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="form-group"><label>Deskripsi *</label><input type="text" name="deskripsi" class="form-control" value="{{ old('deskripsi') }}" required style="border-radius:8px;"></div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('financial.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

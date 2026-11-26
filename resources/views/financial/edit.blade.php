@extends('adminlte::master')
@section('title', 'Edit Transaksi Keuangan')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Transaksi Keuangan</h5><a href="/financial" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                <form method="POST" action="{{ route('financial.update', $transaction) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode Transaksi</label>
                                <input type="text" class="form-control" value="{{ $transaction->kode_transaksi_keu }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe</label>
                                <input type="text" class="form-control" value="{{ ucfirst($transaction->tipe) }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="text" class="form-control" value="Rp {{ number_format($transaction->jumlah,0,',','.') }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <input type="text" class="form-control" value="{{ $transaction->deskripsi }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sumber Dana</label>
                                <input type="text" class="form-control" value="{{ ucfirst(str_replace('_',' ',$transaction->sumber_dana)) }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Metode Pembayaran</label>
                                <input type="text" class="form-control" value="{{ ucfirst($transaction->metode_pembayaran) }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" style="border-radius:8px;">
                                    @foreach(['draft','pending_approval','disetujui','ditolak','selesai'] as $s)
                                    <option value="{{ $s }}" {{ old('status',$transaction->status)==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('financial.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('adminlte::page')

@section('title', 'Tambah Batch Pengolahan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-industry mr-2"></i>Form Tambah Batch Pengolahan</h5>
        <div class="card-tools">
            <a href="{{ route('processing.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('processing.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Batch <span class="text-danger">*</span></label>
                        <input type="text" name="kode_batch" class="form-control" 
                               value="{{ old('kode_batch') }}" required placeholder="contoh: BT-001">
                    </div>
                    <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control" 
                               value="{{ old('nama_produk') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-control" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Pengolahan <span class="text-danger">*</span></label>
                        <select name="jenis_pengolahan" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['pengeringan', 'pengalengan', 'pengemasan', 'fermentasi', 'penggilingan', 'penyaringan', 'pembekuan', 'lainnya'] as $j)
                            <option value="{{ $j }}" {{ old('jenis_pengolahan') == $j ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $j)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Produksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_produksi" class="form-control" 
                               value="{{ old('tanggal_produksi') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kadaluarsa</label>
                        <input type="date" name="tanggal_kadaluarsa" class="form-control" 
                               value="{{ old('tanggal_kadaluarsa') }}">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Produksi</label>
                        <input type="number" name="jumlah_produksi" class="form-control" step="0.01"
                               value="{{ old('jumlah_produksi') }}">
                    </div>
                    <div class="form-group">
                        <label>Satuan Output</label>
                        <input type="text" name="satuan_output" class="form-control" 
                               value="{{ old('satuan_output') }}" placeholder="contoh: kg, pack, liter">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Biaya Produksi</label>
                        <input type="number" name="biaya_produksi" class="form-control" step="0.01"
                               value="{{ old('biaya_produksi') }}">
                    </div>
                    <div class="form-group">
                        <label>Harga Jual Per Satuan</label>
                        <input type="number" name="harga_jual_satuan" class="form-control" step="0.01"
                               value="{{ old('harga_jual_satuan') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="proses" {{ old('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                            <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="gagal" {{ old('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('processing.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

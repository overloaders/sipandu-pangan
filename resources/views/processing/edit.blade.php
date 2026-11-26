@extends('adminlte::page')

@section('title', 'Edit Batch Pengolahan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-industry mr-2"></i>{{ $batch->nama_produk }}</h5>
        <div class="card-tools">
            <a href="{{ route('processing.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('processing.update', $batch->id) }}" method="POST">
        @csrf
        @method('PUT')
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
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control" 
                               value="{{ old('nama_produk', $batch->nama_produk) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-control" required>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $batch->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Pengolahan <span class="text-danger">*</span></label>
                        <select name="jenis_pengolahan" class="form-control" required>
                            @foreach(['pengeringan', 'pengalengan', 'pengemasan', 'fermentasi', 'penggilingan', 'penyaringan', 'pembekuan', 'lainnya'] as $j)
                            <option value="{{ $j }}" {{ old('jenis_pengolahan', $batch->jenis_pengolahan) == $j ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $j)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Produksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_produksi" class="form-control" 
                               value="{{ old('tanggal_produksi', $batch->tanggal_produksi ? \Carbon\Carbon::parse($batch->tanggal_produksi)->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kadaluarsa</label>
                        <input type="date" name="tanggal_kadaluarsa" class="form-control" 
                               value="{{ old('tanggal_kadaluarsa', $batch->tanggal_kadaluarsa ? \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Produksi</label>
                        <input type="number" name="jumlah_produksi" class="form-control" step="0.01"
                               value="{{ old('jumlah_produksi', $batch->jumlah_produksi) }}">
                    </div>
                    <div class="form-group">
                        <label>Satuan Output</label>
                        <input type="text" name="satuan_output" class="form-control" 
                               value="{{ old('satuan_output', $batch->satuan_output) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Biaya Produksi</label>
                        <input type="number" name="biaya_produksi" class="form-control" step="0.01"
                               value="{{ old('biaya_produksi', $batch->biaya_produksi) }}">
                    </div>
                    <div class="form-group">
                        <label>Harga Jual Per Satuan</label>
                        <input type="number" name="harga_jual_satuan" class="form-control" step="0.01"
                               value="{{ old('harga_jual_satuan', $batch->harga_jual_satuan) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach(['draft', 'proses', 'selesai', 'gagal'] as $s)
                            <option value="{{ $s }}" {{ old('status', $batch->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $batch->keterangan) }}</textarea>
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

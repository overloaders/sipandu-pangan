@extends('adminlte::page')

@section('title', 'Tambah Stock Opname')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-clipboard-check mr-2"></i>Form Tambah Stock Opname</h5>
        <div class="card-tools">
            <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('stock-opname.store') }}" method="POST">
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
                        <label>Kode Opname <span class="text-danger">*</span></label>
                        <input type="text" name="kode_opname" class="form-control" 
                               value="{{ old('kode_opname') }}" required placeholder="contoh: SOP-001">
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
                        <label>Tanggal Opname <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_opname" class="form-control" 
                               value="{{ old('tanggal_opname', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Opname <span class="text-danger">*</span></label>
                        <select name="jenis_opname" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['rutin', 'insidental', 'tahunan', 'serah_terima'] as $j)
                            <option value="{{ $j }}" {{ old('jenis_opname') == $j ? 'selected' : '' }}>{{ ucfirst($j) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Petugas WBP</label>
                        <select name="petugas_wbp_id" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($wbps as $w)
                            <option value="{{ $w->id }}" {{ old('petugas_wbp_id') == $w->id ? 'selected' : '' }}>{{ $w->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="proses" {{ old('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                            <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

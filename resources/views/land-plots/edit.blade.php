@extends('adminlte::page')

@section('title', 'Edit Lahan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-map mr-2"></i>{{ $landPlot->nama_lahan }}</h5>
        <div class="card-tools">
            <a href="{{ route('land-plots.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('land-plots.update', $landPlot->id) }}" method="POST">
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
                        <label>Nama Lahan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lahan" class="form-control" 
                               value="{{ old('nama_lahan', $landPlot->nama_lahan) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-control" required>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $landPlot->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Luas (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="luas_m2" class="form-control" step="0.01"
                               value="{{ old('luas_m2', $landPlot->luas_m2) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Tanah</label>
                        <select name="jenis_tanah" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(['tanah_liat', 'tanah_berpasir', 'tanah_laterit', 'tanah_gambut', 'tanah_volkanik', 'campuran'] as $j)
                            <option value="{{ $j }}" {{ old('jenis_tanah', $landPlot->jenis_tanah) == $j ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $j)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sistem Irigasi</label>
                        <select name="sistem_irigasi" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(['tadah_hujan', 'irigasi_tetes', 'parit', 'pompa_air', 'sungai', 'sumur'] as $s)
                            <option value="{{ $s }}" {{ old('sistem_irigasi', $landPlot->sistem_irigasi) == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach(['tersedia', 'ditanami', 'istirahat', 'renovasi', 'tidak_digunakan'] as $s)
                            <option value="{{ $s }}" {{ old('status', $landPlot->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $landPlot->keterangan) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('land-plots.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

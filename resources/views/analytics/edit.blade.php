@extends('adminlte::page')

@section('title', 'Edit Indikator Analitik')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-line mr-2"></i>{{ $analytic->nama_indikator }}</h5>
        <div class="card-tools">
            <a href="{{ route('analytics.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('analytics.update', $analytic->id) }}" method="POST">
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
                        <label>Nama Indikator <span class="text-danger">*</span></label>
                        <input type="text" name="nama_indikator" class="form-control" 
                               value="{{ old('nama_indikator', $analytic->nama_indikator) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['produktivitas', 'kualitas', 'efisiensi', 'nutrisi', 'kesehatan', 'keuangan', 'lingkungan'] as $k)
                            <option value="{{ $k }}" {{ old('kategori', $analytic->kategori) == $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="satuan" class="form-control" 
                               value="{{ old('satuan', $analytic->satuan) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Target Nilai <span class="text-danger">*</span></label>
                        <input type="number" name="target_nilai" class="form-control" step="0.01"
                               value="{{ old('target_nilai', $analytic->target_nilai) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control">
                            <option value="">-- Semua Unit --</option>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $analytic->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ old('is_active', $analytic->is_active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $analytic->is_active) == 0 ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $analytic->deskripsi) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Rumus Perhitungan</label>
                        <input type="text" name="rumus_perhitungan" class="form-control" 
                               value="{{ old('rumus_perhitungan', $analytic->rumus_perhitungan) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Batas Minimum</label>
                        <input type="number" name="batas_minimum" class="form-control" step="0.01"
                               value="{{ old('batas_minimum', $analytic->batas_minimum) }}">
                    </div>
                    <div class="form-group">
                        <label>Batas Maksimum</label>
                        <input type="number" name="batas_maksimum" class="form-control" step="0.01"
                               value="{{ old('batas_maksimum', $analytic->batas_maksimum) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('analytics.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

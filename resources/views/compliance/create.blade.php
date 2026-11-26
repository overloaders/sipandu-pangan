@extends('adminlte::page')

@section('title', 'Tambah Catatan Komplians')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-certificate mr-2"></i>Form Tambah Catatan Komplians</h5>
        <div class="card-tools">
            <a href="{{ route('compliance.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('compliance.store') }}" method="POST">
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
                        <label>Kode Komplians <span class="text-danger">*</span></label>
                        <input type="text" name="kode_komplians" class="form-control" 
                               value="{{ old('kode_komplians') }}" required placeholder="contoh: KMP-001">
                    </div>
                    <div class="form-group">
                        <label>Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" 
                               value="{{ old('judul') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Sertifikasi <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['halal', 'haccp', 'iso22000', 'gap', 'gmp', 'bpom', 'lainnya'] as $t)
                            <option value="{{ $t }}" {{ old('tipe') == $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                            @endforeach
                        </select>
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
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pemeriksaan" class="form-control" 
                               value="{{ old('tanggal_pemeriksaan') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Berlaku Sampai</label>
                        <input type="date" name="tanggal_berlaku_sampai" class="form-control" 
                               value="{{ old('tanggal_berlaku_sampai') }}">
                    </div>
                    <div class="form-group">
                        <label>Nilai Skor</label>
                        <input type="number" name="nilai_skor" class="form-control" step="0.01"
                               value="{{ old('nilai_skor') }}" placeholder="0-100">
                    </div>
                    <div class="form-group">
                        <label>Tingkat Risiko</label>
                        <select name="tingkat_risiko" class="form-control">
                            <option value="rendah" {{ old('tingkat_risiko') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="sedang" {{ old('tingkat_risiko') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="tinggi" {{ old('tingkat_risiko') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="kritis" {{ old('tingkat_risiko') == 'kritis' ? 'selected' : '' }}>Kritis</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hasil</label>
                        <textarea name="hasil" class="form-control" rows="2">{{ old('hasil') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Temuan</label>
                        <textarea name="temuan" class="form-control" rows="2">{{ old('temuan') }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rekomendasi</label>
                        <textarea name="rekomendasi" class="form-control" rows="2">{{ old('rekomendasi') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="perlu_perbaikan" {{ old('status') == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                            <option value="kedaluwarsa" {{ old('status') == 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                            <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('compliance.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

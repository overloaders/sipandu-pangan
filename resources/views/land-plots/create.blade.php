@extends('adminlte::page')

@section('title', 'Tambah Lahan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-map mr-2"></i>Form Tambah Lahan</h5>
        <div class="card-tools">
            <a href="{{ route('land-plots.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('land-plots.store') }}" method="POST">
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
                        <label>Kode Lahan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_lahan" class="form-control" 
                               value="{{ old('kode_lahan') }}" required placeholder="contoh: LH-001">
                    </div>
                    <div class="form-group">
                        <label>Nama Lahan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lahan" class="form-control" 
                               value="{{ old('nama_lahan') }}" required>
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
                        <label>Luas (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="luas_m2" class="form-control" step="0.01"
                               value="{{ old('luas_m2') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Tanah</label>
                        <select name="jenis_tanah" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(['tanah_liat', 'tanah_berpasir', 'tanah_laterit', 'tanah_gambut', 'tanah_volkanik', 'campuran'] as $j)
                            <option value="{{ $j }}" {{ old('jenis_tanah') == $j ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $j)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sistem Irigasi</label>
                        <select name="sistem_irigasi" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(['tadah_hujan', 'irigasi_tetes', 'parit', 'pompa_air', 'sungai', 'sumur'] as $s)
                            <option value="{{ $s }}" {{ old('sistem_irigasi') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="ditanami" {{ old('status') == 'ditanami' ? 'selected' : '' }}>Ditanami</option>
                            <option value="istirahat" {{ old('status') == 'istirahat' ? 'selected' : '' }}>Istirahat</option>
                            <option value="renovasi" {{ old('status') == 'renovasi' ? 'selected' : '' }}>Renovasi</option>
                            <option value="tidak_digunakan" {{ old('status') == 'tidak_digunakan' ? 'selected' : '' }}>Tidak Digunakan</option>
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
            <a href="{{ route('land-plots.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

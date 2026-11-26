@extends('adminlte::page')

@section('title', 'Tambah Program Pelatihan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-graduation-cap mr-2"></i>Form Tambah Program Pelatihan</h5>
        <div class="card-tools">
            <a href="{{ route('training.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('training.store') }}" method="POST">
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
                        <label>Kode Pelatihan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_pelatihan" class="form-control" 
                               value="{{ old('kode_pelatihan') }}" required placeholder="contoh: PLT-001">
                    </div>
                    <div class="form-group">
                        <label>Nama Pelatihan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelatihan" class="form-control" 
                               value="{{ old('nama_pelatihan') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['pertanian', 'peternakan', 'perikanan', 'pengolahan', 'keterampilan', 'keamanan', 'manajemen'] as $k)
                            <option value="{{ $k }}" {{ old('kategori') == $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe Program</label>
                        <select name="tipe" class="form-control">
                            <option value="offline" {{ old('tipe') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="online" {{ old('tipe') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="hybrid" {{ old('tipe') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
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
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" 
                               value="{{ old('lokasi') }}" placeholder="contoh: Aula Gedung B">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control" 
                               value="{{ old('tanggal_mulai') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" 
                               value="{{ old('tanggal_selesai') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jam Pelatihan</label>
                        <input type="number" name="jam_pelatihan" class="form-control" 
                               value="{{ old('jam_pelatihan') }}" placeholder="contoh: 16">
                    </div>
                    <div class="form-group">
                        <label>Kapasitas Peserta</label>
                        <input type="number" name="kapasitas_peserta" class="form-control" 
                               value="{{ old('kapasitas_peserta') }}" placeholder="contoh: 30">
                    </div>
                    <div class="form-group">
                        <label>Biaya</label>
                        <input type="number" name="biaya" class="form-control" step="0.01"
                               value="{{ old('biaya') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="perencanaan" {{ old('status') == 'perencanaan' ? 'selected' : '' }}>Perencanaan</option>
                            <option value="terbuka" {{ old('status') == 'terbuka' ? 'selected' : '' }}>Terbuka</option>
                            <option value="berlangsung" {{ old('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sertifikat Diberikan</label>
                        <select name="sertifikat_diberikan" class="form-control">
                            <option value="1" {{ old('sertifikat_diberikan', 1) ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('sertifikat_diberikan', 1) == 0 ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Materi Pokok</label>
                        <textarea name="materi_pokok" class="form-control" rows="2">{{ old('materi_pokok') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('training.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

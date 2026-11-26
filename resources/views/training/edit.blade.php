@extends('adminlte::page')

@section('title', 'Edit Program Pelatihan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-graduation-cap mr-2"></i>{{ $program->nama_pelatihan }}</h5>
        <div class="card-tools">
            <a href="{{ route('training.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('training.update', $program->id) }}" method="POST">
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
                        <label>Nama Pelatihan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelatihan" class="form-control" 
                               value="{{ old('nama_pelatihan', $program->nama_pelatihan) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control" required>
                            @foreach(['pertanian', 'peternakan', 'perikanan', 'pengolahan', 'keterampilan', 'keamanan', 'manajemen'] as $k)
                            <option value="{{ $k }}" {{ old('kategori', $program->kategori) == $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe Program</label>
                        <select name="tipe" class="form-control">
                            <option value="offline" {{ old('tipe', $program->tipe) == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="online" {{ old('tipe', $program->tipe) == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="hybrid" {{ old('tipe', $program->tipe) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-control" required>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $program->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" 
                               value="{{ old('lokasi', $program->lokasi) }}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control" 
                               value="{{ old('tanggal_mulai', $program->tanggal_mulai ? \Carbon\Carbon::parse($program->tanggal_mulai)->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" 
                               value="{{ old('tanggal_selesai', $program->tanggal_selesai ? \Carbon\Carbon::parse($program->tanggal_selesai)->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jam Pelatihan</label>
                        <input type="number" name="jam_pelatihan" class="form-control" 
                               value="{{ old('jam_pelatihan', $program->jam_pelatihan) }}">
                    </div>
                    <div class="form-group">
                        <label>Kapasitas Peserta</label>
                        <input type="number" name="kapasitas_peserta" class="form-control" 
                               value="{{ old('kapasitas_peserta', $program->kapasitas_peserta) }}">
                    </div>
                    <div class="form-group">
                        <label>Biaya</label>
                        <input type="number" name="biaya" class="form-control" step="0.01"
                               value="{{ old('biaya', $program->biaya) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach(['perencanaan', 'terbuka', 'berlangsung', 'selesai', 'dibatalkan'] as $s)
                            <option value="{{ $s }}" {{ old('status', $program->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sertifikat Diberikan</label>
                        <select name="sertifikat_diberikan" class="form-control">
                            <option value="1" {{ old('sertifikat_diberikan', $program->sertifikat_diberikan) ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('sertifikat_diberikan', $program->sertifikat_diberikan) == 0 ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Materi Pokok</label>
                        <textarea name="materi_pokok" class="form-control" rows="2">{{ old('materi_pokok', $program->materi_pokok) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $program->deskripsi) }}</textarea>
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

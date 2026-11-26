@extends('adminlte::page')

@section('title', 'Edit Kamar')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-door-open mr-2"></i>Edit Kamar di Blok {{ $block->kode_blok }}</h5>
        <div class="card-tools">
            <a href="{{ route('blocks.show', $block) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('blocks.rooms.update', [$block, $room]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode_kamar">Kode Kamar *</label>
                        <input type="text" name="kode_kamar" class="form-control @error('kode_kamar') is-invalid @enderror"
                               value="{{ old('kode_kamar', $room->kode_kamar) }}" required>
                        @error('kode_kamar')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kapasitas">Kapasitas *</label>
                        <input type="number" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror"
                               value="{{ old('kapasitas', $room->kapasitas) }}" min="1" required>
                        @error('kapasitas')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
                            <option value="tersedia" {{ old('status', $room->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terisi" {{ old('status', $room->status) == 'terisi' ? 'selected' : '' }}>Terisi</option>
                            <option value="perawatan" {{ old('status', $room->status) == 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection

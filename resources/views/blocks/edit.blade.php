@extends('adminlte::master')
@section('title', 'Edit Blok')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Blok</h5><a href="/blocks" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                <form method="POST" action="{{ route('blocks.update', $block) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Blok</label><input type="text" name="nama_blok" class="form-control" value="{{ old('nama_blok', $block->nama_blok) }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Kapasitas</label><input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', $block->kapasitas) }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Status</label><select name="status" class="form-control" style="border-radius:8px;">@foreach(['aktif','renovasi','nonaktif'] as $s)<option value="{{ $s }}" {{ old('status',$block->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div></div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="/blocks" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

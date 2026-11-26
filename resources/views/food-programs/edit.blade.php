@extends('adminlte::master')
@section('title', 'Edit Program: ' . $foodProgram->nama_program)
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Program</h5><a href="/food-programs" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('food-programs.update', $foodProgram) }}">
                    @csrf @method('PUT')
                    <div class="form-group"><label>Nama Program</label><input type="text" name="nama_program" class="form-control" value="{{ old('nama_program', $foodProgram->nama_program) }}" style="border-radius:8px;"></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Status</label><select name="status" class="form-control" style="border-radius:8px;">@foreach(['perencanaan','aktif','selesai','dibatalkan'] as $s)<option value="{{ $s }}" {{ old('status',$foodProgram->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Anggaran (Rp)</label><input type="number" name="anggaran" class="form-control" value="{{ old('anggaran', $foodProgram->anggaran) }}" style="border-radius:8px;"></div></div>
                    </div>
                    <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" style="border-radius:8px;">{{ old('deskripsi', $foodProgram->deskripsi) }}</textarea></div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="/food-programs" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

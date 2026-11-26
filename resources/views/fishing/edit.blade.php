@extends('adminlte::master')
@section('title', 'Edit Kolam')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Kolam</h5><a href="{{ route('fish-ponds.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                <form method="POST" action="{{ route('fish-ponds.update', $fishPond) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Kolam</label><input type="text" name="nama_kolam" class="form-control" value="{{ old('nama_kolam', $fishPond->nama_kolam) }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Program Pangan</label><select name="food_program_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih (opsional) --</option>@foreach($foodPrograms as $fp)<option value="{{ $fp->id }}" {{ old('food_program_id', $fishPond->food_program_id)==$fp->id?'selected':'' }}>{{ $fp->nama_program }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Luas (m²)</label><input type="number" name="luas_m2" class="form-control" value="{{ old('luas_m2', $fishPond->luas_m2) }}" step="0.01" style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Status</label><select name="status" class="form-control" style="border-radius:8px;">@foreach(['aktif','kering','renovasi','nonaktif'] as $s)<option value="{{ $s }}" {{ old('status',$fishPond->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div></div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('fish-ponds.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

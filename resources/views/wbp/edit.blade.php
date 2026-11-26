@extends('adminlte::master')
@section('title', 'Edit WBP: ' . $wbp->nama_lengkap)
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-user-edit mr-2"></i>Edit Data WBP</h5><a href="{{ route('wbp.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('wbp.update', $wbp) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $wbp->nama_lengkap) }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Status</label><select name="status" class="form-control" style="border-radius:8px;">@foreach(['aktif','dikeluarkan','pindah','meninggal','bebas'] as $s)<option value="{{ $s }}" {{ old('status',$wbp->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Blok</label><select name="block_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($blocks as $b)<option value="{{ $b->id }}" {{ old('block_id',$wbp->block_id)==$b->id?'selected':'' }}>{{ $b->kode_blok }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Kamar</label><select name="room_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($rooms as $r)<option value="{{ $r->id }}" {{ old('room_id',$wbp->room_id)==$r->id?'selected':'' }}>{{ $r->kode_kamar }}</option>@endforeach</select></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Tamping</label><input type="text" name="tamping" class="form-control" value="{{ old('tamping', $wbp->tamping) }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2" style="border-radius:8px;">{{ old('keterangan', $wbp->keterangan) }}</textarea></div></div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('wbp.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

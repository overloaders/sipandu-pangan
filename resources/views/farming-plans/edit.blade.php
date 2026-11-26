@extends('adminlte::master')
@section('title', 'Edit Rencana Kerja')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Rencana Kerja</h5><a href="/farming-plans" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                <form method="POST" action="{{ route('farming-plans.update', $farmingPlan) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanaman</label><input type="text" class="form-control" value="{{ $farmingPlan->cropType?->nama_tanaman }}" disabled style="border-radius:8px;background:#f8fafc;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Lahan</label><input type="text" class="form-control" value="{{ $farmingPlan->landPlot?->nama_lahan }}" disabled style="border-radius:8px;background:#f8fafc;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Status</label><select name="status" class="form-control" style="border-radius:8px;">@foreach(['direncanakan','proses_tanam','tumbuh','siap_panen','panen','selesai'] as $s)<option value="{{ $s }}" {{ old('status',$farmingPlan->status)==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Tgl Tanam Aktual</label><input type="date" name="tanggal_tanam_aktual" class="form-control" value="{{ old('tanggal_tanam_aktual', $farmingPlan->tanggal_tanam_aktual?->format('Y-m-d')) }}" style="border-radius:8px;"></div></div>
                    </div>
                    <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2" style="border-radius:8px;">{{ old('keterangan', $farmingPlan->keterangan) }}</textarea></div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="/farming-plans" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

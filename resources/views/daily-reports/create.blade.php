@extends('adminlte::master')
@section('title', 'Buat Laporan Harian')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-clipboard-list mr-2"></i>Form Laporan Harian</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('daily-reports.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Tanggal *</label><input type="date" name="tanggal_laporan" class="form-control" value="{{ old('tanggal_laporan', date('Y-m-d')) }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="form-group"><label>Ringkasan Kegiatan</label><textarea name="ringkasan_kegiatan" class="form-control" rows="3" style="border-radius:8px;">{{ old('ringkasan_kegiatan') }}</textarea></div>
                    <div class="form-group"><label>Catatan Khusus</label><textarea name="catatan_khusus" class="form-control" rows="2" style="border-radius:8px;">{{ old('catatan_khusus') }}</textarea></div>
                    <div class="form-group"><label>Masalah Ditemukan</label><textarea name="masalah_ditemukan" class="form-control" rows="2" style="border-radius:8px;">{{ old('masalah_ditemukan') }}</textarea></div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('daily-reports.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

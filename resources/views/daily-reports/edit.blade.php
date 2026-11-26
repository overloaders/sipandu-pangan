@extends('adminlte::master')
@section('title', 'Edit Laporan Harian')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Laporan Harian</h5><a href="/daily-reports" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                <form method="POST" action="{{ route('daily-reports.update', $dailyReport) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode Laporan</label>
                                <input type="text" class="form-control" value="{{ $dailyReport->kode_laporan }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="text" class="form-control" value="{{ $dailyReport->tanggal_laporan->format('d M Y') }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Unit</label>
                                <input type="text" class="form-control" value="{{ $dailyReport->unit?->nama_unit ?? '-' }}" disabled style="border-radius:8px;background:#f8fafc;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" style="border-radius:8px;">
                                    @foreach(['draft','dikirim','dikonfirmasi','dibatalkan'] as $s)
                                    <option value="{{ $s }}" {{ old('status',$dailyReport->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Ringkasan Kegiatan</label>
                        <textarea name="ringkasan_kegiatan" class="form-control" rows="4" style="border-radius:8px;">{{ old('ringkasan_kegiatan', $dailyReport->ringkasan_kegiatan) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('daily-reports.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

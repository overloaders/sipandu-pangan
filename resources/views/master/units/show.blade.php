@extends('adminlte::page')
@section('title', 'Detail Unit / Lapas')
@section('header_actions')
    <a href="{{ route('master.units.edit', $unit) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
    <a href="{{ route('master.units.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-building mr-2"></i>Detail Unit</h5></div>
            <div class="card-body">
                @if($unit->logo_url)
                <div class="text-center mb-3">
                    <img src="{{ $unit->logo_url }}" alt="Logo {{ $unit->nama_unit }}" style="max-height:90px;max-width:180px;border:1px solid #dee2e6;border-radius:6px;padding:6px;background:#fff;">
                </div>
                @endif
                <div class="table-responsive"><table class="table table-bordered">
                    <tr><th width="200">Kode Unit</th><td>{{ $unit->kode_unit }}</td></tr>
                    <tr><th>Nama Unit</th><td>{{ $unit->nama_unit }}</td></tr>
                    <tr><th>Tipe</th><td><span class="badge badge-primary">{{ ucfirst($unit->tipe_unit) }}</span></td></tr>
                    <tr><th>Provinsi</th><td>{{ $unit->provinsi ?? '-' }}</td></tr>
                    <tr><th>Kota/Kab</th><td>{{ $unit->kota_kab ?? '-' }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $unit->alamat ?? '-' }}</td></tr>
                    <tr><th>Telepon</th><td>{{ $unit->telepon ?? '-' }}</td></tr>
                    <tr><th>Email</th><td>{{ $unit->email ?? '-' }}</td></tr>
                    <tr><th>Kapasitas WBP</th><td>{{ $unit->kapasitas_wbp ?? '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $unit->status == 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($unit->status) }}</span></td></tr>
                </table></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-sitemap mr-2"></i>Unit Anak</h5></div>
            <div class="card-body">
                @forelse($unit->children as $child)
                    <div class="mb-2 p-2 bg-light rounded">
                        <strong>{{ $child->nama_unit }}</strong><br>
                        <small class="text-muted">{{ $child->kode_unit }} | {{ ucfirst($child->tipe_unit) }}</small>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada unit anak</p>
                @endforelse
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>WBP ({{ $unit->wbps->count() }})</h5></div>
            <div class="card-body">
                @forelse($unit->wbps->take(10) as $wbp)
                    <div class="mb-1">{{ $wbp->nama_lengkap }} <small class="text-muted">({{ $wbp->nomor_induk }})</small></div>
                @empty
                    <p class="text-muted">Belum ada WBP</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop

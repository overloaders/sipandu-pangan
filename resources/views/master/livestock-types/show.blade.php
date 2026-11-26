@extends('adminlte::page')
@section('title', 'Detail Jenis Ternak')
@section('header_actions')
    <a href="{{ route('master.livestock-types.edit', $livestockType) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
    <a href="{{ route('master.livestock-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-paw mr-2"></i>Detail Jenis Ternak</h5></div>
    <div class="card-body">
        <div class="table-responsive"><table class="table table-bordered">
            <tr><th width="250">Kode Jenis</th><td>{{ $livestockType->kode_jenis }}</td></tr>
            <tr><th>Nama</th><td>{{ $livestockType->nama_jenis }}</td></tr>
            <tr><th>Kategori</th><td><span class="badge badge-info">{{ ucfirst($livestockType->kategori ?? '-') }}</span></td></tr>
            <tr><th>Berat Rata-rata (kg)</th><td>{{ $livestockType->berat_rata_rata ?? '-' }}</td></tr>
            <tr><th>Harga Beli/Ekor</th><td>Rp {{ number_format($livestockType->harga_beli_ekor ?? 0, 0, ',', '.') }}</td></tr>
            <tr><th>Harga Jual/kg</th><td>Rp {{ number_format($livestockType->harga_jual_kg ?? 0, 0, ',', '.') }}</td></tr>
            <tr><th>Masa Pemeliharaan</th><td>{{ $livestockType->masa_pemeliharaan_hari ?? '-' }} hari</td></tr>
            <tr><th>Pakan Utama</th><td>{{ $livestockType->pakan_utama ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="badge badge-{{ ($livestockType->status ?? 'aktif') == 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($livestockType->status ?? 'aktif') }}</span></td></tr>
        </table></div>
    </div>
</div>
@stop

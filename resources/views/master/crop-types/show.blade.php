@extends('adminlte::page')
@section('title', 'Detail Jenis Tanaman')
@section('header_actions')
    <a href="{{ route('master.crop-types.edit', $cropType) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
    <a href="{{ route('master.crop-types.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-seedling mr-2"></i>Detail Jenis Tanaman</h5></div>
    <div class="card-body">
        <div class="table-responsive"><table class="table table-bordered">
            <tr><th width="250">Kode</th><td>{{ $cropType->kode_jenis }}</td></tr>
            <tr><th>Nama</th><td>{{ $cropType->nama_jenis }}</td></tr>
            <tr><th>Nama Latin</th><td><em>{{ $cropType->nama_latin ?? '-' }}</em></td></tr>
            <tr><th>Kategori</th><td><span class="badge badge-success">{{ ucfirst($cropType->kategori ?? '-') }}</span></td></tr>
            <tr><th>Masa Tanam</th><td>{{ $cropType->masa_tanam_hari ?? '-' }} hari</td></tr>
            <tr><th>Hasil/m²</th><td>{{ $cropType->hasil_per_m2 ?? '-' }} kg</td></tr>
            <tr><th>Harga Jual/kg</th><td>Rp {{ number_format($cropType->harga_jual_kg ?? 0, 0, ',', '.') }}</td></tr>
            <tr><th>Musim</th><td>{{ $cropType->musim ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="badge badge-{{ ($cropType->status ?? 'aktif') == 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($cropType->status ?? 'aktif') }}</span></td></tr>
        </table></div>
    </div>
</div>
@stop

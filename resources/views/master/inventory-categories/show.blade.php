@extends('adminlte::page')
@section('title', 'Detail Kategori Inventaris')
@section('header_actions')
    <a href="{{ route('master.inventory-categories.edit', $inventoryCategory) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
    <a href="{{ route('master.inventory-categories.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-tags mr-2"></i>Detail Kategori Inventaris</h5></div>
    <div class="card-body">
        <div class="table-responsive"><table class="table table-bordered">
            <tr><th width="250">Kode</th><td>{{ $inventoryCategory->kode_kategori }}</td></tr>
            <tr><th>Nama</th><td>{{ $inventoryCategory->nama_kategori }}</td></tr>
            <tr><th>Tipe</th><td><span class="badge badge-info">{{ ucfirst($inventoryCategory->tipe_kategori ?? '-') }}</span></td></tr>
            <tr><th>Satuan Standar</th><td>{{ $inventoryCategory->satuan_standar ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="badge badge-{{ ($inventoryCategory->status ?? 'aktif') == 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($inventoryCategory->status ?? 'aktif') }}</span></td></tr>
        </table></div>
    </div>
</div>
@stop

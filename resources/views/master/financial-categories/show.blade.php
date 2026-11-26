@extends('adminlte::page')
@section('title', 'Detail Kategori Keuangan')
@section('header_actions')
    <a href="{{ route('master.financial-categories.edit', $financialCategory) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
    <a href="{{ route('master.financial-categories.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-money-bill mr-2"></i>Detail Kategori Keuangan</h5></div>
    <div class="card-body">
        <div class="table-responsive"><table class="table table-bordered">
            <tr><th width="250">Kode</th><td>{{ $financialCategory->kode_kategori }}</td></tr>
            <tr><th>Nama</th><td>{{ $financialCategory->nama_kategori }}</td></tr>
            <tr><th>Tipe</th><td><span class="badge badge-{{ ($financialCategory->tipe ?? '') == 'pemasukan' ? 'success' : 'danger' }}">{{ ucfirst($financialCategory->tipe ?? '-') }}</span></td></tr>
            <tr><th>Status</th><td><span class="badge badge-{{ ($financialCategory->status ?? 'aktif') == 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($financialCategory->status ?? 'aktif') }}</span></td></tr>
        </table></div>
    </div>
</div>
@stop

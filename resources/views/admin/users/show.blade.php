@extends('adminlte::master')
@section('title', 'Detail User: ' . $user->name)
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-user mr-2"></i>Detail User</h5>
                <div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-edit mr-1"></i> Edit</a>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}','{{ $user->name }}')" title="Hapus"><i class="fas fa-trash"></i></button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="150" style="font-weight:600;color:#64748b;">Nama</td><td style="font-weight:700;">{{ $user->name }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Email</td><td>{{ $user->email }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">NIP</td><td>{{ $user->nip ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Telepon</td><td>{{ $user->phone ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Unit</td><td>{{ $user->unit?->nama_unit ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Role</td><td>@foreach($user->roles as $r)<span class="badge badge-primary mr-1">{{ $r->name }}</span>@endforeach</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $user->is_active?'success':'danger' }}">{{ $user->is_active?'Aktif':'Nonaktif' }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Login Terakhir</td><td>{{ $user->last_login_at?->diffForHumans() ?? '-' }}</td></tr>
                </table></div>
            </div>
        </div>
    </div>
</div>
@endsection

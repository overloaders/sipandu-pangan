@extends('adminlte::master')
@section('title', 'Manajemen Pengguna')
@section('header_actions')
<a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i> Tambah User</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-users-cog mr-2"></i>Daftar Pengguna</h5>
        <form class="form-inline" method="GET">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari user..." value="{{ request('search') }}" style="border-radius:8px;width:200px;">
            <button class="btn btn-sm btn-outline-success ml-1"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nama</th><th>Email</th><th>NIP</th><th>Unit</th><th>Role</th><th>Status</th><th width="100">Aksi</th></tr></thead>
                <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="font-weight:600;">{{ $user->name }}</td>
                    <td style="font-size:12px;">{{ $user->email }}</td>
                    <td style="font-size:12px;">{{ $user->nip ?? '-' }}</td>
                    <td>{{ $user->unit?->nama_unit ?? '-' }}</td>
                    <td>@foreach($user->roles as $role)<span class="badge badge-primary mr-1">{{ $role->name }}</span>@endforeach</td>
                    <td><span class="badge badge-{{ $user->is_active?'success':'danger' }}">{{ $user->is_active?'Aktif':'Nonaktif' }}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-success"><i class="fas fa-edit"></i></a>
                            @if(!$user->hasRole('super-admin'))
                            <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}','{{ $user->name }}')"><i class="fas fa-trash"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4"><p style="color:#94a3b8;">Belum ada user</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())<div class="card-footer">{{ $users->withQueryString()->links() }}</div>@endif
</div>
@endsection

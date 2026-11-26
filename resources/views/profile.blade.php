@extends('adminlte::master')
@section('title', 'Profil')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;background:#d1fae5;color:#059669;font-size:32px;font-weight:700;">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <h5 style="font-weight:700;">{{ $user->name }}</h5>
                <p style="color:#64748b;font-size:13px;">{{ $user->email }}</p>
                @foreach($user->roles as $role)<span class="badge badge-primary">{{ $role->name }}</span> @endforeach
                <div class="mt-3" style="font-size:12px;color:#94a3b8;">
                    <div><i class="fas fa-building mr-1"></i> {{ $user->unit?->nama_unit ?? '-' }}</div>
                    <div><i class="fas fa-clock mr-1"></i> Login terakhir: {{ $user->last_login_at?->diffForHumans() ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-user-edit mr-2"></i>Edit Profil</h5></div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success" style="border-radius:8px;">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" style="border-radius:8px;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled style="border-radius:8px;background:#f8fafc;">
                    </div>
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" style="border-radius:8px;">
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

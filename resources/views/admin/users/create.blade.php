@extends('adminlte::master')
@section('title', 'Tambah User')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-user-plus mr-2"></i>Form User Baru</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>NIP</label><input type="text" name="nip" class="form-control" value="{{ old('nip') }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Unit</label><select name="unit_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Password *</label><input type="password" name="password" class="form-control" required style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Konfirmasi Password *</label><input type="password" name="password_confirmation" class="form-control" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        @foreach($roles as $role)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="role_{{ $role->id }}" name="role_ids[]" value="{{ $role->id }}" {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="role_{{ $role->id }}" style="font-size:13px;">{{ $role->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

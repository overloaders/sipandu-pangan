@extends('adminlte::master')
@section('title', 'Edit User: ' . $user->name)
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title" style="margin:0;"><i class="fas fa-user-edit mr-2"></i>Edit User</h5><a href="/admin/users" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Nama</label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" style="border-radius:8px;"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Unit</label><select name="unit_id" class="form-control" style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}" {{ old('unit_id',$user->unit_id)==$u->id?'selected':'' }}>{{ $u->nama_unit }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Status</label><select name="is_active" class="form-control" style="border-radius:8px;"><option value="1" {{ old('is_active',$user->is_active)?'selected':'' }}>Aktif</option><option value="0" {{ !old('is_active',$user->is_active)?'selected':'' }}>Nonaktif</option></select></div></div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        @foreach($roles as $role)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="role_{{ $role->id }}" name="role_ids[]" value="{{ $role->id }}" {{ in_array($role->id, old('role_ids', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="role_{{ $role->id }}" style="font-size:13px;">{{ $role->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="/admin/users" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

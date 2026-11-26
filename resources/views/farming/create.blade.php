@extends('adminlte::master')
@section('title', 'Tambah Rencana Kerja')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-tractor mr-2"></i>Form Rencana Kerja Pertanian</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;"><ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('farming-plans.store') }}">
                    @csrf
                    <div class="form-group"><label>Kode Rencana *</label><input type="text" name="kode_rencana" class="form-control" value="{{ old('kode_rencana') }}" required style="border-radius:8px;"></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Program *</label><select name="food_program_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($programs as $p)<option value="{{ $p->id }}">{{ $p->nama_program }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Lahan *</label><select name="land_plot_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($landPlots as $l)<option value="{{ $l->id }}">{{ $l->nama_lahan }}</option>@endforeach</select></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanaman *</label><select name="crop_type_id" class="form-control" required style="border-radius:8px;"><option value="">-- Pilih --</option>@foreach($cropTypes as $c)<option value="{{ $c->id }}">{{ $c->nama_tanaman }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Jumlah Tanaman *</label><input type="number" name="jumlah_tanaman" class="form-control" value="{{ old('jumlah_tanaman') }}" min="1" required style="border-radius:8px;"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Tanggal Tanam *</label><input type="date" name="tanggal_tanam_rencana" class="form-control" value="{{ old('tanggal_tanam_rencana', date('Y-m-d')) }}" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Luas Tanam (m²) *</label><input type="number" name="luas_tanam_m2" class="form-control" value="{{ old('luas_tanam_m2') }}" step="0.01" min="0" required style="border-radius:8px;"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Metode *</label><select name="metode_budi_daya" class="form-control" required style="border-radius:8px;"><option value="konvensional">Konvensional</option><option value="organik">Organik</option><option value="hidroponik">Hidroponik</option><option value="aeroponik">Aeroponik</option><option value="mixed">Mixed</option></select></div></div>
                    </div>
                    <button type="submit" class="btn btn-success" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('farming-plans.index') }}" class="btn btn-secondary" style="border-radius:8px;">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('adminlte::page')

@section('title', 'Edit Rencana Gizi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-utensils mr-2"></i>{{ $plan->nama_menu }}</h5>
        <div class="card-tools">
            <a href="{{ route('nutrition.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <form action="{{ route('nutrition.update', $plan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" class="form-control" 
                               value="{{ old('nama_menu', $plan->nama_menu) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-control" required>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $plan->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Waktu Makan <span class="text-danger">*</span></label>
                        <select name="waktu_makan" class="form-control" required>
                            @foreach(['sarapan', 'makan_siang', 'makan_malam', 'snack_pagi', 'snack_sore'] as $w)
                            <option value="{{ $w }}" {{ old('waktu_makan', $plan->waktu_makan) == $w ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $w)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe Porsi <span class="text-danger">*</span></label>
                        <select name="tipe_porsi" class="form-control" required>
                            @foreach(['reguler', 'vitamin', 'diet', 'pasien', 'lansia'] as $t)
                            <option value="{{ $t }}" {{ old('tipe_porsi', $plan->tipe_porsi) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kalori Target (kcal)</label>
                        <input type="number" name="kalori_target_kcal" class="form-control" step="0.01"
                               value="{{ old('kalori_target_kcal', $plan->kalori_target_kcal) }}">
                    </div>
                    <div class="form-group">
                        <label>Protein Target (gr)</label>
                        <input type="number" name="protein_target_gr" class="form-control" step="0.01"
                               value="{{ old('protein_target_gr', $plan->protein_target_gr) }}">
                    </div>
                    <div class="form-group">
                        <label>Karbohidrat Target (gr)</label>
                        <input type="number" name="karbohidrat_target_gr" class="form-control" step="0.01"
                               value="{{ old('karbohidrat_target_gr', $plan->karbohidrat_target_gr) }}">
                    </div>
                    <div class="form-group">
                        <label>Lemak Target (gr)</label>
                        <input type="number" name="lemak_target_gr" class="form-control" step="0.01"
                               value="{{ old('lemak_target_gr', $plan->lemak_target_gr) }}">
                    </div>
                    <div class="form-group">
                        <label>Serat Target (gr)</label>
                        <input type="number" name="serat_target_gr" class="form-control" step="0.01"
                               value="{{ old('serat_target_gr', $plan->serat_target_gr) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Berlaku Mulai</label>
                        <input type="date" name="berlaku_mulai" class="form-control" 
                               value="{{ old('berlaku_mulai', $plan->berlaku_mulai ? \Carbon\Carbon::parse($plan->berlaku_mulai)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="form-group">
                        <label>Berlaku Sampai</label>
                        <input type="date" name="berlaku_selesai" class="form-control" 
                               value="{{ old('berlaku_selesai', $plan->berlaku_selesai ? \Carbon\Carbon::parse($plan->berlaku_selesai)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Estimasi Biaya per Porsi</label>
                        <input type="number" name="estimasi_biaya_per_porsi" class="form-control" step="0.01"
                               value="{{ old('estimasi_biaya_per_porsi', $plan->estimasi_biaya_per_porsi) }}">
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            @foreach(['draft', 'aktif', 'nonaktif'] as $s)
                            <option value="{{ $s }}" {{ old('status', $plan->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Sumber Bahan</label>
                <input type="text" name="sumber_bahan" class="form-control" 
                       value="{{ old('sumber_bahan', $plan->sumber_bahan) }}">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $plan->deskripsi) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('nutrition.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

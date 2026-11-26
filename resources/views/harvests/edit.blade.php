@extends('adminlte::master')

@section('title', 'Edit Data Panen ' . $harvest->kode_panen)

@section('header_actions')
<a href="{{ route('harvests.index') }}" class="btn btn-secondary btn-sm">
    <i class="fas fa-arrow-left mr-1"></i> Kembali
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-edit mr-2"></i>Edit Data Panen: {{ $harvest->kode_panen }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('harvests.update', $harvest) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Panen</label>
                        <input type="text" class="form-control" value="{{ $harvest->kode_panen }}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rencana Kerja <span style="color:red;">*</span></label>
                        <select name="farming_plan_id" class="form-control @error('farming_plan_id') is-invalid @enderror" required>
                            @foreach($farmingPlans as $fp)
                            <option value="{{ $fp->id }}" {{ old('farming_plan_id', $harvest->farming_plan_id)==$fp->id?'selected':'' }}>
                                {{ $fp->kode_rencana }} - {{ $fp->cropType->nama_tanaman ?? '-' }}
                            </option>
                            @endforeach
                        </select>
                        @error('farming_plan_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Panen <span style="color:red;">*</span></label>
                        <input type="date" name="tanggal_panen" class="form-control @error('tanggal_panen') is-invalid @enderror"
                            value="{{ old('tanggal_panen', $harvest->tanggal_panen?->format('Y-m-d')) }}" required>
                        @error('tanggal_panen') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Total Berat (kg) <span style="color:red;">*</span></label>
                        <input type="number" step="0.01" name="total_berat_kg" class="form-control @error('total_berat_kg') is-invalid @enderror"
                            value="{{ old('total_berat_kg', $harvest->total_berat_kg) }}" required min="0">
                        @error('total_berat_kg') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kualitas <span style="color:red;">*</span></label>
                        <select name="kualitas" class="form-control @error('kualitas') is-invalid @enderror" required>
                            @foreach(['A','B','C','D'] as $k)
                            <option value="{{ $k }}" {{ old('kualitas', $harvest->kualitas)==$k?'selected':'' }}>Kualitas {{ $k }}</option>
                            @endforeach
                        </select>
                        @error('kualitas') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Harga Jual/kg (Rp) <span style="color:red;">*</span></label>
                        <input type="number" step="0.01" name="harga_jual_per_kg" class="form-control @error('harga_jual_per_kg') is-invalid @enderror"
                            value="{{ old('harga_jual_per_kg', $harvest->harga_jual_per_kg) }}" required min="0">
                        @error('harga_jual_per_kg') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Total Pendapatan (Rp)</label>
                        <input type="number" step="0.01" name="total_pendapatan" class="form-control @error('total_pendapatan') is-invalid @enderror"
                            value="{{ old('total_pendapatan', $harvest->total_pendapatan) }}" min="0">
                        @error('total_pendapatan') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $harvest->keterangan) }}</textarea>
                @error('keterangan') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('harvests.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection

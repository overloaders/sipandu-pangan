@extends('adminlte::master')

@section('title', 'Tambah Data Panen')

@section('header_actions')
<a href="{{ route('harvests.index') }}" class="btn btn-secondary btn-sm">
    <i class="fas fa-arrow-left mr-1"></i> Kembali
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-plus-circle mr-2"></i>Tambah Data Hasil Panen</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('harvests.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Panen <span style="color:red;">*</span></label>
                        <input type="text" name="kode_panen" class="form-control @error('kode_panen') is-invalid @enderror"
                            value="{{ old('kode_panen', 'PAN-' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT)) }}" required>
                        @error('kode_panen') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rencana Kerja <span style="color:red;">*</span></label>
                        <select name="farming_plan_id" class="form-control @error('farming_plan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Rencana Kerja --</option>
                            @foreach($farmingPlans as $fp)
                            <option value="{{ $fp->id }}" {{ old('farming_plan_id')==$fp->id?'selected':'' }}>
                                {{ $fp->kode_rencana }} - {{ $fp->cropType->nama_tanaman ?? '-' }} ({{ $fp->landPlot->nama_lahan ?? '-' }})
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
                            value="{{ old('tanggal_panen', date('Y-m-d')) }}" required>
                        @error('tanggal_panen') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Total Berat (kg) <span style="color:red;">*</span></label>
                        <input type="number" step="0.01" name="total_berat_kg" class="form-control @error('total_berat_kg') is-invalid @enderror"
                            value="{{ old('total_berat_kg') }}" required min="0">
                        @error('total_berat_kg') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kualitas <span style="color:red;">*</span></label>
                        <select name="kualitas" class="form-control @error('kualitas') is-invalid @enderror" required>
                            @foreach(['A','B','C','D'] as $k)
                            <option value="{{ $k }}" {{ old('kualitas')==$k?'selected':'' }}>Kualitas {{ $k }}</option>
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
                            value="{{ old('harga_jual_per_kg') }}" required min="0">
                        @error('harga_jual_per_kg') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Total Pendapatan (Rp)</label>
                        <input type="number" step="0.01" name="total_pendapatan" class="form-control @error('total_pendapatan') is-invalid @enderror"
                            value="{{ old('total_pendapatan') }}" min="0">
                        @error('total_pendapatan') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Catatan tentang panen ini...">{{ old('keterangan') }}</textarea>
                @error('keterangan') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('harvests.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

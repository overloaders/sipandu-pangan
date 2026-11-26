@extends('adminlte::master')

@section('title', 'Detail Panen ' . $harvest->kode_panen)

@section('header_actions')
<a href="{{ route('reports.harvest-detail', array_merge([$harvest->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-file-pdf mr-1"></i> PDF
</a>
<a href="{{ route('harvests.index') }}" class="btn btn-secondary btn-sm">
    <i class="fas fa-arrow-left mr-1"></i> Kembali
</a>
<a href="{{ route('harvests.edit', $harvest) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit mr-1"></i> Edit
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-apple-alt mr-2"></i>Detail Panen: {{ $harvest->kode_panen }}</h5>
                @php
                    $colors = ['A'=>'#059669','B'=>'#2563eb','C'=>'#d97706','D'=>'#dc2626'];
                @endphp
                <span class="badge" style="background:{{ $colors[$harvest->kualitas] ?? '#6b7280' }};color:#fff;padding:6px 14px;border-radius:12px;font-size:13px;">
                    Kualitas {{ $harvest->kualitas }}
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless">
                    <tr>
                        <td style="width:200px;color:#64748b;font-weight:600;">Kode Panen</td>
                        <td><strong>{{ $harvest->kode_panen }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-weight:600;">Tanggal Panen</td>
                        <td>{{ $harvest->tanggal_panen?->format('d F Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-weight:600;">Rencana Kerja</td>
                        <td>
                            @if($harvest->farmingPlan)
                            <a href="{{ route('farming-plans.show', array_merge([$harvest->farmingPlan], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}">{{ $harvest->farmingPlan->kode_rencana }}</a>
                            - {{ $harvest->farmingPlan->cropType->nama_tanaman ?? '-' }}
                            @else - @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-weight:600;">Lahan</td>
                        <td>{{ $harvest->farmingPlan?->landPlot?->nama_lahan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-weight:600;">Total Berat</td>
                        <td><strong style="font-size:18px;">{{ number_format($harvest->total_berat_kg, 2, ',', '.') }} kg</strong></td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-weight:600;">Harga Jual/kg</td>
                        <td>Rp {{ number_format($harvest->harga_jual_per_kg, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;font-weight:600;">Total Pendapatan</td>
                        <td style="color:#059669;font-weight:700;font-size:16px;">Rp {{ number_format($harvest->total_pendapatan, 0, ',', '.') }}</td>
                    </tr>
                    @if($harvest->keterangan)
                    <tr>
                        <td style="color:#64748b;font-weight:600;">Keterangan</td>
                        <td>{{ $harvest->keterangan }}</td>
                    </tr>
                    @endif
                </table></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-calculator mr-2"></i>Ringkasan</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="font-size:36px;font-weight:700;color:#10b981;">{{ number_format($harvest->total_berat_kg, 0, ',', '.') }}</div>
                    <div style="color:#64748b;font-size:13px;">kg total panen</div>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color:#64748b;">Harga per kg</span>
                    <strong>Rp {{ number_format($harvest->harga_jual_per_kg, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color:#64748b;">Kualitas</span>
                    <strong>{{ $harvest->kualitas }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color:#64748b;">Program</span>
                    <strong>{{ $harvest->farmingPlan?->foodProgram?->nama_program ?? '-' }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span style="color:#64748b;font-weight:600;">Total Pendapatan</span>
                    <strong style="color:#059669;font-size:16px;">Rp {{ number_format($harvest->total_pendapatan, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-info-circle mr-2"></i>Info Sistem</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <div>Dibuat: {{ $harvest->created_at?->format('d M Y H:i') ?? '-' }}</div>
                    <div>Diperbarui: {{ $harvest->updated_at?->format('d M Y H:i') ?? '-' }}</div>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

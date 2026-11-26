@extends('adminlte::master')
@section('title', 'Detail Rencana: ' . $farmingPlan->kode_rencana)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-tractor mr-2"></i>Detail Rencana Kerja</h5>
                <div>
                    <a href="{{ route('reports.farming-plan', array_merge([$farmingPlan], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                    <a href="{{ route('farming-plans.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Rencana</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $farmingPlan->kode_rencana }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Program</td><td>{{ $farmingPlan->foodProgram?->nama_program ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Lahan</td><td>{{ $farmingPlan->landPlot?->nama_lahan ?? '-' }} ({{ number_format($farmingPlan->landPlot?->luas_m2 ?? 0,0,',','.') }} m²)</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tanaman</td><td style="font-weight:700;">{{ $farmingPlan->cropType?->nama_tanaman ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jumlah Tanaman</td><td>{{ number_format($farmingPlan->jumlah_tanaman) }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Metode</td><td><span class="badge badge-info">{{ ucfirst($farmingPlan->metode_budi_daya) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-success">{{ ucfirst(str_replace('_',' ',$farmingPlan->status)) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tgl Tanam</td><td>{{ $farmingPlan->tanggal_tanam_rencana?->format('d M Y') ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Estimasi Hasil</td><td>{{ number_format($farmingPlan->estimasi_hasil_kg ?? 0,0,',','.') }} kg</td></tr>
                </table></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-list mr-2"></i>Kegiatan ({{ $farmingPlan->activities->count() }})</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Tanggal</th><th>Kegiatan</th><th>PIC</th><th>Biaya</th></tr></thead><tbody>
                @forelse($farmingPlan->activities as $a)
                <tr><td style="font-size:12px;">{{ $a->tanggal_kegiatan->format('d/m/Y') }}</td><td><span class="badge badge-info">{{ ucfirst(str_replace('_',' ',$a->jenis_kegiatan)) }}</span></td><td>{{ $a->picWbp?->nama_lengkap ?? '-' }}</td><td>Rp {{ number_format($a->biaya,0,',','.') }}</td></tr>
                @empty <tr><td colspan="4" class="text-center" style="color:#94a3b8;">Belum ada kegiatan</td></tr> @endforelse
                </tbody></table></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-apple-alt mr-2"></i>Panen</h5></div>
            <div class="card-body">
                @forelse($farmingPlan->harvests as $h)
                <div class="p-2 mb-2" style="background:#d1fae5;border-radius:8px;font-size:12px;">
                    <div style="font-weight:700;color:#059669;">{{ number_format($h->total_berat_kg,0,',','.') }} kg</div>
                    <div>{{ $h->tanggal_panen->format('d M Y') }} — Rp {{ number_format($h->total_pendapatan,0,',','.') }}</div>
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada panen</p> @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

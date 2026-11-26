@extends('adminlte::page')

@section('title', 'Dashboard Analitik')



@section('content')
{{-- FILTER BAR --}}
<div class="row mb-3 filter-header">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 style="font-weight:700;margin:0;"><i class="fas fa-tachometer-alt text-primary"></i> Dashboard Analitik</h4>
                <p style="color:#64748b;font-size:13px;margin:2px 0 0;">Ringkasan indikator kinerja dan pencapaian target</p>
            </div>
            <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                @if($units)
                <select id="filter-unit" class="form-control form-control-sm" style="border-radius:8px;max-width:200px;">
                    <option value="" {{ empty($selectedUnitId) ? 'selected' : '' }}>Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ $selectedUnitId == $unit->id ? 'selected' : '' }}>{{ $unit->nama_unit }}</option>
                    @endforeach
                </select>
                @endif
                <a href="{{ route('analytics.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i> Daftar Indikator</a>
                <a href="{{ route('reports.analytics', ['unit_id' => $selectedUnitId]) }}" class="btn btn-success btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
    </div>
</div>

@php
    $activeIndicators = $indicators->where('is_active', true);
    $byKategori = $activeIndicators->groupBy('kategori');
    
    // Calculate totals from targets table
    $allTargets = $activeIndicators->flatMap->targets;
    $totalTarget = $allTargets->sum('target_nilai');
    $totalRealisasi = $allTargets->sum('realisasi_nilai');
    $overallPct = $totalTarget > 0 ? ($totalRealisasi / $totalTarget) * 100 : 0;
    
    // Group by kategori with actual targets
    $kategoriData = [];
    foreach ($byKategori as $kategori => $items) {
        $katTargets = $items->flatMap->targets;
        $kategoriData[$kategori] = [
            'count' => $items->count(),
            'target' => $katTargets->sum('target_nilai'),
            'realisasi' => $katTargets->sum('realisasi_nilai'),
        ];
    }
@endphp

{{-- STAT CARDS --}}
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Indikator</span>
                <span class="info-box-number">{{ $activeIndicators->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-bullseye"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Target</span>
                <span class="info-box-number">{{ number_format($totalTarget, 1) }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Realisasi</span>
                <span class="info-box-number">{{ number_format($totalRealisasi, 1) }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-{{ $overallPct >= 80 ? 'success' : ($overallPct >= 50 ? 'warning' : 'danger') }}">
                <i class="fas fa-percentage"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Pencapaian Overall</span>
                <span class="info-box-number">{{ number_format($overallPct, 1) }}%</span>
            </div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="row">
    {{-- CHART 1: Pencapaian Target per Indikator (Bar) --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-bar text-info"></i> Pencapaian Target vs Realisasi</h5>
            </div>
            <div class="card-body">
                <canvas id="chartTargetVsRealisasi" height="300"></canvas>
            </div>
        </div>
    </div>

    {{-- CHART 2: Distribusi per Kategori (Doughnut) --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-pie text-warning"></i> Distribusi per Kategori</h5>
            </div>
            <div class="card-body">
                <canvas id="chartKategori" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- CHART 3: Target per Kategori (Horizontal Bar) --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-balance-scale text-success"></i> Target vs Realisasi per Kategori</h5>
            </div>
            <div class="card-body">
                <canvas id="chartKategoriTarget" height="250"></canvas>
            </div>
        </div>
    </div>

    {{-- CHART 4: Status Capaian (Doughnut) --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-tachometer-alt text-danger"></i> Status Capaian Indikator</h5>
            </div>
            <div class="card-body">
                <canvas id="chartCapaian" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- CHART 5: Radar --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-spider text-primary"></i> Radar Capaian Indikator</h5>
            </div>
            <div class="card-body">
                <canvas id="chartRadar" height="300"></canvas>
            </div>
        </div>
    </div>

    {{-- CHART 6: Persentase per Indikator --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-area text-secondary"></i> Persentase Realisasi per Indikator</h5>
            </div>
            <div class="card-body">
                <canvas id="chartPersentase" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- TABEL DETAIL --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-table text-info"></i> Detail Semua Indikator</h5>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Indikator</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Pencapaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeIndicators as $i => $ind)
                        @php
                            $indTarget = $ind->targets->sum('target_nilai');
                            $indRealisasi = $ind->targets->sum('realisasi_nilai');
                            $pct = $indTarget > 0 ? ($indRealisasi / $indTarget) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="badge badge-info">{{ $ind->kode_indikator }}</span></td>
                            <td>{{ $ind->nama_indikator }}</td>
                            <td><span class="badge badge-secondary">{{ ucfirst($ind->kategori) }}</span></td>
                            <td>{{ $ind->satuan }}</td>
                            <td>{{ number_format($indTarget, 2) }}</td>
                            <td>{{ number_format($indRealisasi, 2) }}</td>
                            <td>
                                @if($pct >= 100)
                                    <span class="badge badge-success">{{ number_format($pct, 1) }}%</span>
                                @elseif($pct >= 75)
                                    <span class="badge badge-warning">{{ number_format($pct, 1) }}%</span>
                                @else
                                    <span class="badge badge-danger">{{ number_format($pct, 1) }}%</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada indikator aktif</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
// Prepare data
const labels = {!! json_encode($activeIndicators->pluck('nama_indikator')->toArray()) !!};
const targets = {!! json_encode($activeIndicators->map(fn($ind) => round($ind->targets->sum('target_nilai'), 2))->toArray()) !!};
const realisasi = {!! json_encode($activeIndicators->map(fn($ind) => round($ind->targets->sum('realisasi_nilai'), 2))->toArray()) !!};
const kategoriLabels = {!! json_encode(array_keys($kategoriData)) !!};
const kategoriCounts = {!! json_encode(array_column($kategoriData, 'count')) !!};
const kategoriTargets = {!! json_encode(array_column($kategoriData, 'target')) !!};
const kategoriRealisasi = {!! json_encode(array_column($kategoriData, 'realisasi')) !!};

// Capaian status
const pctData = targets.map((t, i) => t > 0 ? Math.round((realisasi[i] / t) * 100) : 0);
const achieved = pctData.filter(p => p >= 100).length;
const partial = pctData.filter(p => p >= 50 && p < 100).length;
const low = pctData.filter(p => p < 50).length;

const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#f97316', '#84cc16', '#6366f1'];

// Chart 1: Target vs Realisasi (Bar)
new Chart(document.getElementById('chartTargetVsRealisasi'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Target',
                data: targets,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: '#3b82f6',
                borderWidth: 1
            },
            {
                label: 'Realisasi',
                data: realisasi,
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: '#10b981',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});

// Chart 2: Distribusi Kategori (Doughnut)
new Chart(document.getElementById('chartKategori'), {
    type: 'doughnut',
    data: {
        labels: kategoriLabels,
        datasets: [{
            data: kategoriCounts,
            backgroundColor: colors.slice(0, kategoriLabels.length)
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

// Chart 3: Target per Kategori (Grouped Bar)
new Chart(document.getElementById('chartKategoriTarget'), {
    type: 'bar',
    data: {
        labels: kategoriLabels,
        datasets: [
            {
                label: 'Target',
                data: kategoriTargets,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: '#3b82f6',
                borderWidth: 1
            },
            {
                label: 'Realisasi',
                data: kategoriRealisasi,
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: '#10b981',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: { legend: { position: 'top' } },
        scales: { x: { beginAtZero: true } }
    }
});

// Chart 4: Status Capaian (Doughnut)
new Chart(document.getElementById('chartCapaian'), {
    type: 'doughnut',
    data: {
        labels: ['Tercapai (≥100%)', 'Sebagian (50-99%)', 'Kurang (<50%)'],
        datasets: [{
            data: [achieved, partial, low],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

// Chart 5: Radar
new Chart(document.getElementById('chartRadar'), {
    type: 'radar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Target',
                data: targets,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: '#3b82f6',
                pointBackgroundColor: '#3b82f6'
            },
            {
                label: 'Realisasi',
                data: realisasi,
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderColor: '#10b981',
                pointBackgroundColor: '#10b981'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { r: { beginAtZero: true } }
    }
});

// Chart 6: Persentase Realisasi per Indikator (Bar)
new Chart(document.getElementById('chartPersentase'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Pencapaian (%)',
            data: pctData,
            backgroundColor: pctData.map(p => p >= 100 ? 'rgba(16,185,129,0.7)' : p >= 75 ? 'rgba(245,158,11,0.7)' : 'rgba(239,68,68,0.7)'),
            borderColor: pctData.map(p => p >= 100 ? '#10b981' : p >= 75 ? '#f59e0b' : '#ef4444'),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.parsed.y + '%'
                }
            }
        },
        scales: {
            y: { beginAtZero: true, max: Math.max(120, ...pctData) },
            x: { ticks: { maxRotation: 45, minRotation: 45 } }
        }
    }
});
</script>

@if($units)
<script>
document.getElementById('filter-unit').addEventListener('change', function() {
    const unitId = this.value;
    let url = '/analytics-dashboard';
    if (unitId) url += '?unit_id=' + unitId;
    window.location.href = url;
});
</script>
@endif
@endpush

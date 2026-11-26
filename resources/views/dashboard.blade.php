@extends('adminlte::master')

@section('title', 'Dashboard')

@section('content')
<style>
/* === Skeleton loading === */
.skel{position:relative;overflow:hidden;background:#e2e8f0;border-radius:6px;display:inline-block;vertical-align:middle;}
.skel::after{content:'';position:absolute;inset:0;transform:translateX(-100%);background:linear-gradient(90deg,transparent,rgba(255,255,255,.65),transparent);animation:skelShimmer 1.3s infinite;}
@keyframes skelShimmer{100%{transform:translateX(100%);}}
.skel-block{display:block;width:100%;height:100%;border-radius:6px;}
#page-skeleton{display:none;position:fixed;inset:0;z-index:9999;background:#f1f5f9;overflow:auto;padding:24px;}
#page-skeleton .card{border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 1px 2px rgba(0,0,0,.04);}
#page-skeleton .ps-title{width:220px;height:26px;margin-bottom:4px;}
#page-skeleton .ps-sub{width:340px;height:13px;margin-bottom:24px;}
</style>
<div class="row mb-3 filter-header">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 style="font-weight:700;margin:0;">Dashboard</h4>
                <p style="color:#64748b;font-size:13px;margin:2px 0 0;">Ringkasan data ketahanan pangan hari ini</p>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                    <div class="d-flex align-items-center">
                        <label style="font-size:12px;color:#64748b;margin:0 6px 0 0;">Dari:</label>
                        <input type="date" id="date-from" class="form-control form-control-sm" style="border-radius:8px;width:140px;" value="{{ request('from_date', now()->subMonths(11)->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="d-flex align-items-center">
                        <label style="font-size:12px;color:#64748b;margin:0 6px 0 0;">Sampai:</label>
                        <input type="date" id="date-to" class="form-control form-control-sm" style="border-radius:8px;width:140px;" value="{{ request('to_date', now()->format('Y-m-d')) }}">
                    </div>
                    @if(auth()->user()->hasRole('super-admin') && isset($units))
                    <select id="filter-unit" class="form-control form-control-sm" style="border-radius:8px;width:160px;">
                        <option value="">Semua Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                    @endif
                    <button id="btn-filter-date" class="btn btn-sm btn-primary" style="border-radius:8px;"><i class="fas fa-filter mr-1"></i> Filter</button>
                    <button id="btn-reset-date" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="fas fa-undo"></i></button>
                    <a id="btn-export-pdf" href="{{ route('reports.summary', ['from_date' => request('from_date', now()->subMonths(11)->startOfMonth()->format('Y-m-d')), 'to_date' => request('to_date', now()->format('Y-m-d')), 'unit_id' => request('unit_id')]) }}" class="btn btn-info btn-sm" target="_blank">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                    <span id="refresh-indicator" class="text-muted" style="font-size:11px;"></span>
                    <span class="badge badge-success" style="font-size:11px;padding:4px 8px;">
                        <i class="fas fa-sync-alt mr-1" style="font-size:8px;"></i> 60s
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stat Cards Row 1 --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Total WBP Aktif</div>
                    <div class="stat-value" id="stat-wbp">{{ number_format($wbpAktif) }}</div>
                    <div class="stat-change up"><i class="fas fa-users"></i> Warga binaan</div>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#059669;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Program Aktif</div>
                    <div class="stat-value" id="stat-program">{{ number_format($programAktif) }}</div>
                    <div class="stat-change up"><i class="fas fa-seedling"></i> Berjalan</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#2563eb;">
                    <i class="fas fa-seedling"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Total Panen (kg)</div>
                    <div class="stat-value" id="stat-panen">{{ number_format($panenBulanIni ?? 0, 0, ',', '.') }}</div>
                    @php $change = $panenChange ?? 0; @endphp
                    <div class="stat-change {{ $change >= 0 ? 'up' : 'down' }}" style="font-size:12px;" id="change-panen">
                        <i class="fas fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}" style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};"></i>
                        <span style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};">{{ $change >= 0 ? '+' : '' }}{{ $change }}%</span> vs bulan lalu
                    </div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
                    <i class="fas fa-apple-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value" style="font-size:20px;" id="stat-pendapatan">Rp {{ number_format($pendapatanBulan ?? 0, 0, ',', '.') }}</div>
                    @php $change = $pendapatanChange ?? 0; @endphp
                    <div class="stat-change {{ $change >= 0 ? 'up' : 'down' }}" style="font-size:12px;" id="change-pendapatan">
                        <i class="fas fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}" style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};"></i>
                        <span style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};">{{ $change >= 0 ? '+' : '' }}{{ $change }}%</span> vs bulan lalu
                    </div>
                </div>
                <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Flow Cards (Kledo style) --}}
<div class="row mb-4 dash-row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-project-diagram mr-2"></i>Alur Produksi Pangan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="flow-card" style="background:linear-gradient(135deg,#f59e0b,#f97316);">
                            <i class="fas fa-map"></i>
                            <div class="flow-value" id="stat-lahan">{{ number_format($lahanTersedia) }}</div>
                            <div class="flow-label">Lahan Tersedia</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="flow-card" style="background:linear-gradient(135deg,#ec4899,#f43f5e);">
                            <i class="fas fa-seedling"></i>
                            <div class="flow-value" id="stat-tanam">{{ number_format($aktifTanam) }}</div>
                            <div class="flow-label">Sedang Ditanam</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="flow-card" style="background:linear-gradient(135deg,#06b6d4,#0891b2);">
                            <i class="fas fa-fish"></i>
                            <div class="flow-value" id="stat-ikan">{{ number_format($totalIkan ?? 0) }}</div>
                            <div class="flow-label">Total Ikan Hidup</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="flow-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                            <i class="fas fa-paw"></i>
                            <div class="flow-value" id="stat-ternak">{{ number_format($totalTernak ?? 0) }}</div>
                            <div class="flow-label">Total Ternak</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-8 col-md-12 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-bar mr-2"></i>Rekap Panen per Bulan</h5>
                <span class="badge badge-secondary">12 Bulan Terakhir</span>
            </div>
            <div class="card-body">
                <canvas id="harvestChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-pie mr-2"></i>Distribusi Program</h5>
            </div>
            <div class="card-body">
                <canvas id="programChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Second Row: Livestock & Farming --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-6 col-md-12 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-paw mr-2"></i>Distribusi Peternakan</h5>
            </div>
            <div class="card-body">
                <canvas id="livestockChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-12 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-tractor mr-2"></i>Status Rencana Kerja</h5>
            </div>
            <div class="card-body">
                <canvas id="farmingChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Third Row: Inventory & Finance --}}
<div class="row dash-row">
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Item Inventaris</div>
                    <div class="stat-value" id="stat-items">{{ number_format($totalItems) }}</div>
                    <div id="stat-stok">
                    @if($stokMenipis > 0)
                    <div style="font-size:12px;color:#ef4444;margin-top:4px;"><i class="fas fa-exclamation-circle"></i> {{ $stokMenipis }} item stok menipis</div>
                    @else
                    <div style="font-size:12px;color:#059669;margin-top:4px;"><i class="fas fa-check-circle"></i> Stok aman</div>
                    @endif
                    </div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#dc2626;">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Pemasukan Bulan Ini</div>
                    <div class="stat-value" style="font-size:20px;color:#059669;" id="stat-pemasukan">Rp {{ number_format($pemasukanBulan ?? 0, 0, ',', '.') }}</div>
                    @php $change = $pemasukanChange ?? 0; @endphp
                    <div style="font-size:12px;margin-top:4px;" id="change-pemasukan">
                        <i class="fas fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}" style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};"></i>
                        <span style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};">{{ $change >= 0 ? '+' : '' }}{{ $change }}%</span> vs bln lalu
                    </div>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#059669;">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Pengeluaran Bulan Ini</div>
                    <div class="stat-value" style="font-size:20px;color:#dc2626;" id="stat-pengeluaran">Rp {{ number_format($pengeluaranBulan ?? 0, 0, ',', '.') }}</div>
                    @php $change = $pengeluaranChange ?? 0; @endphp
                    <div style="font-size:12px;margin-top:4px;" id="change-pengeluaran">
                        <i class="fas fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}" style="color:{{ $change >= 0 ? '#dc2626' : '#059669' }};"></i>
                        <span style="color:{{ $change >= 0 ? '#dc2626' : '#059669' }};">{{ $change >= 0 ? '+' : '' }}{{ $change }}%</span> vs bln lalu
                    </div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#dc2626;">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Fourth Row: Reports --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Laporan Hari Ini</div>
                    <div class="stat-value" id="stat-laporan">{{ number_format($laporanHariIni) }}</div>
                    @php $change = $laporanChange ?? 0; @endphp
                    <div style="font-size:12px;" id="change-laporan">
                        <i class="fas fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}" style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};"></i>
                        <span style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};">{{ $change >= 0 ? '+' : '' }}{{ $change }}%</span> vs bln lalu
                    </div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#2563eb;">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Distribusi Bulan Ini</div>
                    <div class="stat-value" id="stat-distribusi">{{ number_format($distribusiBulan) }}</div>
                    @php $change = $distribusiChange ?? 0; @endphp
                    <div style="font-size:12px;" id="change-distribusi">
                        <i class="fas fa-arrow-{{ $change >= 0 ? 'up' : 'down' }}" style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};"></i>
                        <span style="color:{{ $change >= 0 ? '#059669' : '#dc2626' }};">{{ $change >= 0 ? '+' : '' }}{{ $change }}%</span> vs bln lalu
                    </div>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#059669;">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Menu Aktif</div>
                    <div class="stat-value">{{ number_format($totalMenu) }}</div>
                    <div style="font-size:12px;color:#64748b;"><i class="fas fa-utensils"></i> Avg {{ number_format($avgKalori ?? 0, 0) }} kkal</div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Batch Olahan</div>
                    <div class="stat-value">{{ number_format($batchSelesai) }}</div>
                    <div style="font-size:12px;color:#64748b;"><i class="fas fa-industry"></i> {{ $batchAktif }} sedang diproses</div>
                </div>
                <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;">
                    <i class="fas fa-industry"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Fifth Row: Training & Compliance --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Program Pelatihan</div>
                    <div class="stat-value">{{ number_format($programPelatihanAktif) }}</div>
                    <div style="font-size:12px;color:#64748b;"><i class="fas fa-graduation-cap"></i> {{ $totalPeserta }} peserta</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#2563eb;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Sertifikasi Aktif</div>
                    <div class="stat-value">{{ number_format($sertifikasiAktif) }}</div>
                    <div style="font-size:12px;color:{{ $sertifikasiKadaluarsa > 0 ? '#dc2626' : '#059669' }};"><i class="fas fa-certificate"></i> {{ $sertifikasiKadaluarsa }} kadaluarsa</div>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#059669;">
                    <i class="fas fa-certificate"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Indikator Aktif</div>
                    <div class="stat-value">{{ number_format($totalIndikator) }}</div>
                    <div style="font-size:12px;color:#64748b;"><i class="fas fa-chart-line"></i> Monitoring</div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Stock Opname</div>
                    <div class="stat-value">{{ number_format($opnameBulan) }}</div>
                    <div style="font-size:12px;color:#64748b;"><i class="fas fa-clipboard-check"></i> Bulan ini</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2;color:#dc2626;">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sixth Row: New Module Charts --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-graduation-cap mr-2"></i>Pelatihan per Tipe</h5></div>
            <div class="card-body"><canvas id="trainingChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-certificate mr-2"></i>Sertifikasi per Tipe</h5></div>
            <div class="card-body"><canvas id="complianceChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-truck mr-2"></i>Distribusi per Penerima</h5></div>
            <div class="card-body"><canvas id="distributionChart" height="200"></canvas></div>
        </div>
    </div>
</div>
{{-- Seventh Row: Financial Trend & Daily Distribution Charts --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-8 col-md-12 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-line mr-2"></i>Tren Keuangan 12 Bulan</h5>
                <span class="badge badge-success">Pemasukan vs Pengeluaran</span>
            </div>
            <div class="card-body">
                <canvas id="financeChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-calendar-day mr-2"></i>Distribusi Harian</h5>
                <span class="badge badge-info">30 Hari Terakhir</span>
            </div>
            <div class="card-body">
                <canvas id="dailyDistChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>
--}}
{{-- Eighth Row: Program Trend + Nutrition + Analytics Charts --}}
<div class="row mb-4 dash-row">
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-line mr-2"></i>Tren Program Pangan</h5>
                <span class="badge badge-primary">12 Bulan</span>
            </div>
            <div class="card-body">
                <canvas id="programTrendChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-utensils mr-2"></i>Nutrisi & Pengolahan</h5>
                <span class="badge badge-warning">Kombinasi</span>
            </div>
            <div class="card-body">
                <canvas id="nutritionChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-bar mr-2"></i>Indikator Analitik</h5>
                <span class="badge badge-info">Kategori</span>
            </div>
            <div class="card-body">
                <canvas id="analyticsChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Full-page skeleton shown while navigating (Filter/Reset) --}}
<div id="page-skeleton" aria-hidden="true">
    <div class="container-fluid">
        <div class="skel ps-title"></div>
        <div class="skel ps-sub"></div>
        <div class="row">
            @for($i = 0; $i < 4; $i++)
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card"><div class="card-body">
                    <div class="skel" style="width:110px;height:12px;margin-bottom:14px;"></div>
                    <div class="skel" style="width:90px;height:26px;margin-bottom:12px;"></div>
                    <div class="skel" style="width:150px;height:12px;"></div>
                </div></div>
            </div>
            @endfor
        </div>
        <div class="row">
            <div class="col-xl-8 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header"><div class="skel" style="width:180px;height:18px;"></div></div>
                    <div class="card-body"><div class="skel skel-block" style="height:260px;"></div></div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header"><div class="skel" style="width:150px;height:18px;"></div></div>
                    <div class="card-body"><div class="skel skel-block" style="height:260px;"></div></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header"><div class="skel" style="width:160px;height:18px;"></div></div>
                    <div class="card-body"><div class="skel skel-block" style="height:200px;"></div></div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header"><div class="skel" style="width:160px;height:18px;"></div></div>
                    <div class="card-body"><div class="skel skel-block" style="height:200px;"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.dashCharts = window.dashCharts || {};
    // === HARVEST CHART ===
    const harvestCtx = document.getElementById('harvestChart');
    if (harvestCtx) {
        window.dashCharts.harvest = new Chart(harvestCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($harvestMonths) !!},
                datasets: [{
                    label: 'Panen (kg)',
                    data: {!! json_encode($harvestData) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const months = {!! json_encode($harvestMonths) !!};
                        const label = months[idx] || '';
                        const parts = label.split(' ');
                        const monthMap = {'Jan':'01','Feb':'02','Mar':'03','Apr':'04','May':'05','Jun':'06','Jul':'07','Aug':'08','Sep':'09','Oct':'10','Nov':'11','Dec':'12'};
                        const m = monthMap[parts[0]] || '01';
                        const y = parseInt(parts[1], 10) || new Date().getFullYear();
                        const lastDay = new Date(y, parseInt(m, 10), 0).getDate();
                        const from = y + '-' + m + '-01';
                        const to = y + '-' + m + '-' + String(lastDay).padStart(2, '0');
                        window.location.href = '/harvests?from_date=' + from + '&to_date=' + to;
                    }
                },
                plugins: { legend: { display: false }, tooltip: { callbacks: { title: function(items) { return 'Klik untuk detail: ' + items[0].label; } } } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, cursor: 'pointer' } }
                }
            }
        });
    }

    // === PROGRAM DISTRIBUTION CHART ===
    const progCtx = document.getElementById('programChart');
    if (progCtx) {
        const progLabels = {!! json_encode($programDistribution->keys()) !!};
        const progData = {!! json_encode($programDistribution->values()) !!};
        const progColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444'];
        
        window.dashCharts.program = new Chart(progCtx, {
            type: 'doughnut',
            data: {
                labels: progLabels.length ? progLabels : ['Belum ada data'],
                datasets: [{
                    data: progData.length ? progData : [1],
                    backgroundColor: progLabels.length ? progColors.slice(0, progLabels.length) : ['#e2e8f0'],
                    borderWidth: 0,
                    spacing: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const labels = {!! json_encode($programDistribution->keys()) !!};
                        const jenis = labels[idx] || '';
                        window.location.href = '/food-programs?jenis=' + encodeURIComponent(jenis);
                    }
                },
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12, usePointStyle: true, cursor: 'pointer' } },
                    tooltip: { callbacks: { title: function(items) { return 'Klik untuk detail: ' + items[0].label; } } }
                }
            }
        });
    }

    // === LIVESTOCK CHART ===
    const liveCtx = document.getElementById('livestockChart');
    if (liveCtx) {
        const liveLabels = {!! json_encode($livestockByType->keys()) !!};
        const liveData = {!! json_encode($livestockByType->values()) !!};
        const liveColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444', '#84cc16', '#f97316'];
        
        window.dashCharts.livestock = new Chart(liveCtx, {
            type: 'bar',
            data: {
                labels: liveLabels.length ? liveLabels : ['Belum ada data'],
                datasets: [{
                    label: 'Jumlah',
                    data: liveData.length ? liveData : [0],
                    backgroundColor: liveLabels.length ? liveColors.slice(0, liveLabels.length) : ['#e2e8f0'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        window.location.href = '/livestock-barns';
                    }
                },
                plugins: { legend: { display: false }, tooltip: { callbacks: { title: function(items) { return 'Klik untuk lihat kandang'; } } } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 }, cursor: 'pointer' } }
                }
            }
        });
    }

    // === FARMING STATUS CHART ===
    const farmCtx = document.getElementById('farmingChart');
    if (farmCtx) {
        const farmLabels = {!! json_encode($farmingStatus->keys()) !!};
        const farmData = {!! json_encode($farmingStatus->values()) !!};
        const farmColors = ['#f59e0b', '#10b981', '#3b82f6', '#ec4899', '#06b6d4', '#8b5cf6'];
        const farmLabelsId = farmLabels.map(s => {
            const map = {
                'direncanakan': 'Direncanakan',
                'proses_tanam': 'Proses Tanam',
                'tumbuh': 'Tumbuh',
                'siap_panen': 'Siap Panen',
                'panen': 'Panen',
                'selesai': 'Selesai'
            };
            return map[s] || s;
        });
        
        window.dashCharts.farming = new Chart(farmCtx, {
            type: 'doughnut',
            data: {
                labels: farmLabelsId.length ? farmLabelsId : ['Belum ada data'],
                datasets: [{
                    data: farmData.length ? farmData : [1],
                    backgroundColor: farmLabels.length ? farmColors.slice(0, farmLabels.length) : ['#e2e8f0'],
                    borderWidth: 0,
                    spacing: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const labels = {!! json_encode($farmingStatus->keys()) !!};
                        const status = labels[idx] || '';
                        window.location.href = '/farming-plans?status=' + encodeURIComponent(status);
                    }
                },
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12, usePointStyle: true, cursor: 'pointer' } },
                    tooltip: { callbacks: { title: function(items) { return 'Klik untuk detail: ' + items[0].label; } } }
                }
            }
        });
    }

    // === TRAINING CHART ===
    const trainCtx = document.getElementById('trainingChart');
    if (trainCtx) {
        const trainLabels = {!! json_encode($pelatihanByTipe->keys()) !!};
        const trainData = {!! json_encode($pelatihanByTipe->values()) !!};
        const trainColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'];
        
        window.dashCharts.training = new Chart(trainCtx, {
            type: 'doughnut',
            data: {
                labels: trainLabels.length ? trainLabels.map(s => s.replace(/_/g, ' ')) : ['Belum ada data'],
                datasets: [{
                    data: trainData.length ? trainData : [1],
                    backgroundColor: trainLabels.length ? trainColors.slice(0, trainLabels.length) : ['#e2e8f0'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const labels = {!! json_encode($pelatihanByTipe->keys()) !!};
                        const tipe = labels[idx] || '';
                        window.location.href = '/training?tipe=' + encodeURIComponent(tipe);
                    }
                },
                plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8, usePointStyle: true, cursor: 'pointer' } }, tooltip: { callbacks: { title: function(items) { return 'Klik untuk detail: ' + items[0].label; } } } }
            }
        });
    }

    // === COMPLIANCE CHART ===
    const compCtx = document.getElementById('complianceChart');
    if (compCtx) {
        const compLabels = {!! json_encode($sertifikasiByTipe->keys()) !!};
        const compData = {!! json_encode($sertifikasiByTipe->values()) !!};
        const compColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'];
        
        window.dashCharts.compliance = new Chart(compCtx, {
            type: 'bar',
            data: {
                labels: compLabels.length ? compLabels.map(s => s.replace(/_/g, ' ')) : ['Belum ada data'],
                datasets: [{
                    label: 'Jumlah',
                    data: compData.length ? compData : [0],
                    backgroundColor: compLabels.length ? compColors.slice(0, compLabels.length) : ['#e2e8f0'],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const labels = {!! json_encode($sertifikasiByTipe->keys()) !!};
                        const tipe = labels[idx] || '';
                        window.location.href = '/compliance?tipe=' + encodeURIComponent(tipe);
                    }
                },
                plugins: { legend: { display: false }, tooltip: { callbacks: { title: function(items) { return 'Klik untuk detail: ' + items[0].label; } } } },
                scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 10 }, cursor: 'pointer' } } }
            }
        });
    }

    // === DISTRIBUTION CHART ===
    const distCtx = document.getElementById('distributionChart');
    if (distCtx) {
        const distLabels = {!! json_encode($distribusiByTipe->keys()) !!};
        const distData = {!! json_encode($distribusiByTipe->values()) !!};
        const distColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'];
        
        window.dashCharts.distribution = new Chart(distCtx, {
            type: 'pie',
            data: {
                labels: distLabels.length ? distLabels.map(s => s.replace(/_/g, ' ')) : ['Belum ada data'],
                datasets: [{
                    data: distData.length ? distData : [1],
                    backgroundColor: distLabels.length ? distColors.slice(0, distLabels.length) : ['#e2e8f0'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const labels = {!! json_encode($distribusiByTipe->keys()) !!};
                        const tipe = labels[idx] || '';
                        window.location.href = '/distributions?tipe_distribusi=' + encodeURIComponent(tipe);
                    }
                },
                plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8, usePointStyle: true, cursor: 'pointer' } }, tooltip: { callbacks: { title: function(items) { return 'Klik untuk detail: ' + items[0].label; } } } }
            }
        });
    }
    // === FINANCIAL TREND CHART ===
    const finCtx = document.getElementById('financeChart');
    if (finCtx) {
        window.dashCharts.finance = new Chart(finCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($financeMonths) !!},
                datasets: [{
                    label: 'Pemasukan',
                    data: {!! json_encode($incomeData) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                }, {
                    label: 'Pengeluaran',
                    data: {!! json_encode($expenseData) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const months = {!! json_encode($financeMonths) !!};
                        const label = months[idx] || '';
                        const parts = label.split(' ');
                        const monthMap = {'Jan':'01','Feb':'02','Mar':'03','Apr':'04','May':'05','Jun':'06','Jul':'07','Aug':'08','Sep':'09','Oct':'10','Nov':'11','Dec':'12'};
                        const m = monthMap[parts[0]] || '01';
                        const y = parseInt(parts[1], 10) || new Date().getFullYear();
                        const lastDay = new Date(y, parseInt(m, 10), 0).getDate();
                        const from = y + '-' + m + '-01';
                        const to = y + '-' + m + '-' + String(lastDay).padStart(2, '0');
                        window.location.href = '/financial?from_date=' + from + '&to_date=' + to;
                    }
                },
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 12 }, padding: 16, usePointStyle: true, cursor: 'pointer' } },
                    tooltip: {
                        callbacks: {
                            title: function(items) { return 'Klik untuk detail: ' + items[0].label; },
                            label: function(ctx) {
                                return ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11 },
                            callback: function(v) { return 'Rp ' + (v/1000000).toFixed(0) + 'jt'; }
                        }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, cursor: 'pointer' } }
                }
            }
        });
    }

    // === FOOD PROGRAM TREND CHART ===
    const trendCtx = document.getElementById('programTrendChart');
    if (trendCtx) {
        window.dashCharts.programTrend = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($programTrendMonths) !!},
                datasets: [{
                    label: 'Program Baru',
                    data: {!! json_encode($programTrendData) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.15)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    borderWidth: 2.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        window.location.href = '/food-programs';
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(items) { return 'Klik untuk lihat program: ' + items[0].label; },
                            label: function(ctx) { return ctx.parsed.y + ' program baru'; }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 }, stepSize: 1 }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, cursor: 'pointer' } }
                }
            }
        });
    }

    // === NUTRITION + PROCESSING CHART ===
    const nutCtx = document.getElementById('nutritionChart');
    if (nutCtx) {
        const nutLabels = {!! json_encode($menuByTipe->keys()->merge($nutritionByTipe->keys())) !!};
        const nutData = {!! json_encode($menuByTipe->values()->merge($nutritionByTipe->values())) !!};
        const nutColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444', '#84cc16'];
        
        window.dashCharts.nutrition = new Chart(nutCtx, {
            type: 'polarArea',
            data: {
                labels: nutLabels.length ? nutLabels.map(s => s.replace(/_/g, ' ')) : ['Belum ada data'],
                datasets: [{
                    data: nutData.length ? nutData : [1],
                    backgroundColor: nutLabels.length ? nutColors.slice(0, nutLabels.length).map(c => c + '99') : ['#e2e8f099'],
                    borderColor: nutLabels.length ? nutColors.slice(0, nutLabels.length) : ['#e2e8f0'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        window.location.href = '/nutrition';
                    }
                },
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8, usePointStyle: true, cursor: 'pointer' } },
                    tooltip: { callbacks: { title: function(items) { return 'Klik untuk lihat nutrisi'; } } }
                },
                scales: {
                    r: {
                        ticks: { display: false },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    }

    // === ANALYTICS INDICATORS CHART ===
    const anaCtx = document.getElementById('analyticsChart');
    if (anaCtx) {
        const anaLabels = {!! json_encode($analyticsByKategori->keys()) !!};
        const anaData = {!! json_encode($analyticsByKategori->values()) !!};
        const anaColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444'];
        
        window.dashCharts.analytics = new Chart(anaCtx, {
            type: 'bar',
            data: {
                labels: anaLabels.length ? anaLabels.map(s => s.replace(/_/g, ' ')) : ['Belum ada data'],
                datasets: [{
                    label: 'Indikator',
                    data: anaData.length ? anaData : [0],
                    backgroundColor: anaLabels.length ? anaColors.slice(0, anaLabels.length) : ['#e2e8f0'],
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const labels = {!! json_encode($analyticsByKategori->keys()) !!};
                        const kategori = labels[idx] || '';
                        window.location.href = '/analytics?kategori=' + encodeURIComponent(kategori);
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(items) { return 'Klik untuk detail: ' + items[0].label; },
                            label: function(ctx) { return ctx.parsed.y + ' indikator aktif'; }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 }, stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, maxRotation: 45, cursor: 'pointer' }
                    }
                }
            }
        });
    }

    // === DAILY DISTRIBUTION CHART ===
    const dailyCtx = document.getElementById('dailyDistChart');
    if (dailyCtx) {
        window.dashCharts.dailyDist = new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyDistDates) !!},
                datasets: [{
                    label: 'Distribusi',
                    data: {!! json_encode($dailyDistData) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 3,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const dates = {!! json_encode($dailyDistDates) !!};
                        if (dates[idx]) {
                            const parts = dates[idx].split(' ');
                            const monthMap = {'Jan':'01','Feb':'02','Mar':'03','Apr':'04','May':'05','Jun':'06','Jul':'07','Aug':'08','Sep':'09','Oct':'10','Nov':'11','Dec':'12'};
                            const m = monthMap[parts[1]] || '01';
                            const y = parseInt(parts[2], 10) || new Date().getFullYear();
                            const lastDay = new Date(y, parseInt(m, 10), 0).getDate();
                            window.location.href = '/distributions?from_date=' + y + '-' + m + '-01&to_date=' + y + '-' + m + '-' + String(lastDay).padStart(2, '0');
                        }
                    }
                },
                plugins: { legend: { display: false }, tooltip: { callbacks: { title: function(items) { return 'Klik untuk detail: ' + items[0].label; } } } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 10 }, stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 45,
                            autoSkip: true,
                            maxTicksLimit: 15,
                            cursor: 'pointer'
                        }
                    }
                }
            }
        });
    }
    // === DATE RANGE FILTER ===
    function showPageSkeleton() {
        const sk = document.getElementById('page-skeleton');
        if (sk) sk.style.display = 'block';
    }
    document.getElementById('btn-filter-date').addEventListener('click', function() {
        showPageSkeleton();
        const from = document.getElementById('date-from').value;
        const to = document.getElementById('date-to').value;
        const unitEl = document.getElementById('filter-unit');
        const unitId = unitEl ? unitEl.value : '';
        let url = '/dashboard?from_date=' + from + '&to_date=' + to;
        if (unitId) url += '&unit_id=' + unitId;
        window.location.href = url;
    });
    document.getElementById('btn-reset-date').addEventListener('click', function() {
        showPageSkeleton();
        window.location.href = '/dashboard';
    });

    // === AUTO-REFRESH EVERY 60 SECONDS ===
    function formatRupiah(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    }
    function formatNumber(val) {
        return Number(val).toLocaleString('id-ID');
    }
    function getArrowHtml(change) {
        if (change > 0) return '<i class="fas fa-arrow-up" style="color:#059669;"></i> <span style="color:#059669;">+' + change + '%</span>';
        if (change < 0) return '<i class="fas fa-arrow-down" style="color:#dc2626;"></i> <span style="color:#dc2626;">' + change + '%</span>';
        return '<i class="fas fa-minus" style="color:#64748b;"></i> <span style="color:#64748b;">0%</span>';
    }
    function getExpenseArrowHtml(change) {
        if (change > 0) return '<i class="fas fa-arrow-up" style="color:#dc2626;"></i> <span style="color:#dc2626;">+' + change + '%</span>';
        if (change < 0) return '<i class="fas fa-arrow-down" style="color:#059669;"></i> <span style="color:#059669;">' + change + '%</span>';
        return '<i class="fas fa-minus" style="color:#64748b;"></i> <span style="color:#64748b;">0%</span>';
    }

    let lastRefresh = new Date();
    function updateRefreshIndicator() {
        const el = document.getElementById('refresh-indicator');
        if (el) el.textContent = 'Terakhir: ' + lastRefresh.toLocaleTimeString('id-ID');
    }

    // === CHART UPDATE HELPERS ===
    function setPieData(chart, labels, data, colors) {
        if (labels.length) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.data.datasets[0].backgroundColor = colors.slice(0, labels.length);
        } else {
            chart.data.labels = ['Belum ada data'];
            chart.data.datasets[0].data = [1];
            chart.data.datasets[0].backgroundColor = ['#e2e8f0'];
        }
        chart.update();
    }
    function setBarData(chart, labels, data, colors) {
        if (labels.length) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.data.datasets[0].backgroundColor = colors.slice(0, labels.length);
        } else {
            chart.data.labels = ['Belum ada data'];
            chart.data.datasets[0].data = [0];
            chart.data.datasets[0].backgroundColor = ['#e2e8f0'];
        }
        chart.update();
    }
    function updateCharts(c) {
        if (!c) return;
        const pieColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444', '#84cc16'];
        const barColors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444', '#84cc16', '#f97316'];
        const ch = window.dashCharts;

        if (ch.harvest) { ch.harvest.data.labels = c.harvestMonths; ch.harvest.data.datasets[0].data = c.harvestData; ch.harvest.update(); }
        if (ch.program) setPieData(ch.program, Object.keys(c.programDistribution || {}), Object.values(c.programDistribution || {}), pieColors);
        if (ch.livestock) setBarData(ch.livestock, Object.keys(c.livestockByType || {}), Object.values(c.livestockByType || {}), barColors);
        if (ch.farming) {
            const keys = Object.keys(c.farmingStatus || {});
            const map = {'direncanakan': 'Direncanakan', 'proses_tanam': 'Proses Tanam', 'tumbuh': 'Tumbuh', 'siap_panen': 'Siap Panen', 'panen': 'Panen', 'selesai': 'Selesai'};
            setPieData(ch.farming, keys.map(s => map[s] || s), Object.values(c.farmingStatus || {}), pieColors);
        }
        if (ch.training) setPieData(ch.training, Object.keys(c.pelatihanByTipe || {}).map(s => s.replace(/_/g, ' ')), Object.values(c.pelatihanByTipe || {}), pieColors);
        if (ch.compliance) setBarData(ch.compliance, Object.keys(c.sertifikasiByTipe || {}).map(s => s.replace(/_/g, ' ')), Object.values(c.sertifikasiByTipe || {}), barColors);
        if (ch.distribution) setPieData(ch.distribution, Object.keys(c.distribusiByTipe || {}).map(s => s.replace(/_/g, ' ')), Object.values(c.distribusiByTipe || {}), pieColors);
        if (ch.finance) { ch.finance.data.labels = c.financeMonths; ch.finance.data.datasets[0].data = c.incomeData; ch.finance.data.datasets[1].data = c.expenseData; ch.finance.update(); }
        if (ch.programTrend) { ch.programTrend.data.labels = c.programTrendMonths; ch.programTrend.data.datasets[0].data = c.programTrendData; ch.programTrend.update(); }
        if (ch.nutrition) {
            const mKeys = Object.keys(c.menuByTipe || {});
            const nKeys = Object.keys(c.nutritionByTipe || {});
            const labels = mKeys.concat(nKeys).map(s => s.replace(/_/g, ' '));
            const data = Object.values(c.menuByTipe || {}).concat(Object.values(c.nutritionByTipe || {}));
            const colors = pieColors.slice(0, labels.length);
            if (labels.length) {
                ch.nutrition.data.labels = labels;
                ch.nutrition.data.datasets[0].data = data;
                ch.nutrition.data.datasets[0].backgroundColor = colors.map(x => x + '99');
                ch.nutrition.data.datasets[0].borderColor = colors;
            } else {
                ch.nutrition.data.labels = ['Belum ada data'];
                ch.nutrition.data.datasets[0].data = [1];
                ch.nutrition.data.datasets[0].backgroundColor = ['#e2e8f099'];
                ch.nutrition.data.datasets[0].borderColor = ['#e2e8f0'];
            }
            ch.nutrition.update();
        }
        if (ch.analytics) setBarData(ch.analytics, Object.keys(c.analyticsByKategori || {}).map(s => s.replace(/_/g, ' ')), Object.values(c.analyticsByKategori || {}), barColors);
        if (ch.dailyDist) { ch.dailyDist.data.labels = c.dailyDistDates; ch.dailyDist.data.datasets[0].data = c.dailyDistData; ch.dailyDist.update(); }
    }

    // === SKELETON DURING AUTO-REFRESH ===
    let refreshSkelTimer = null;
    let refreshSkelShown = false;
    function showCardSkeleton() {
        document.querySelectorAll('.stat-value').forEach(el => {
            if (el.dataset.skel) return;
            el.dataset.skel = el.innerHTML;
            el.innerHTML = '<span class="skel" style="width:76px;height:24px;"></span>';
        });
        document.querySelectorAll('.stat-change, #stat-stok').forEach(el => {
            if (!el || el.dataset.skel) return;
            el.dataset.skel = el.innerHTML;
            el.innerHTML = '<span class="skel" style="width:120px;height:13px;"></span>';
        });
        document.querySelectorAll('.card canvas').forEach(c => { c.style.opacity = '0.12'; });
    }
    function hideCardSkeleton() {
        document.querySelectorAll('[data-skel]').forEach(el => {
            el.innerHTML = el.dataset.skel;
            delete el.dataset.skel;
        });
        document.querySelectorAll('.card canvas').forEach(c => { c.style.opacity = ''; });
    }

    function refreshDashboard() {
        refreshSkelTimer = setTimeout(function() { refreshSkelShown = true; showCardSkeleton(); }, 250);
        // Bawa filter aktif (tanggal & unit) supaya angka & chart sesuai tampilan
        const refreshParams = new URLSearchParams();
        const fEl = document.getElementById('date-from');
        const tEl = document.getElementById('date-to');
        const uEl = document.getElementById('filter-unit');
        if (fEl && fEl.value) refreshParams.set('from_date', fEl.value);
        if (tEl && tEl.value) refreshParams.set('to_date', tEl.value);
        if (uEl && uEl.value) refreshParams.set('unit_id', uEl.value);
        const qs = refreshParams.toString();
        fetch('{{ route("dashboard.stats") }}' + (qs ? '?' + qs : ''))
            .then(r => r.json())
            .then(d => {
                clearTimeout(refreshSkelTimer);
                if (refreshSkelShown) { hideCardSkeleton(); refreshSkelShown = false; }
                // Update stat cards
                const setHtml = (id, html) => { const el = document.getElementById(id); if (el) el.innerHTML = html; };
                const setText = (id, text, num) => { const el = document.getElementById(id); if (el) {
                    if (typeof num === 'number' && typeof countUpElement === 'function') {
                        countUpElement(el, { to: num, from: 'current', duration: 600 });
                    } else {
                        el.textContent = text;
                    }
                }};

                setText('stat-wbp', formatNumber(d.wbp_aktif), d.wbp_aktif);
                setText('stat-program', formatNumber(d.program_aktif), d.program_aktif);
                setText('stat-panen', formatNumber(d.panen_bulan_ini), d.panen_bulan_ini);
                setText('stat-pendapatan', formatRupiah(d.pendapatan_bulan), d.pendapatan_bulan);
                setText('stat-pemasukan', formatRupiah(d.pemasukan_bulan), d.pemasukan_bulan);
                setText('stat-pengeluaran', formatRupiah(d.pengeluaran_bulan), d.pengeluaran_bulan);
                setText('stat-distribusi', formatNumber(d.distribusi_bulan), d.distribusi_bulan);
                setText('stat-laporan', formatNumber(d.laporan_hari_ini), d.laporan_hari_ini);
                setText('stat-items', formatNumber(d.total_items), d.total_items);
                setText('stat-lahan', formatNumber(d.lahan_tersedia), d.lahan_tersedia);
                setText('stat-tanam', formatNumber(d.aktif_tanam), d.aktif_tanam);
                setText('stat-ikan', formatNumber(d.total_ikan), d.total_ikan);
                setText('stat-ternak', formatNumber(d.total_ternak), d.total_ternak);

                // Update change indicators
                setHtml('change-panen', getArrowHtml(d.panen_change) + ' vs bulan lalu');
                setHtml('change-pendapatan', getArrowHtml(d.pendapatan_change) + ' vs bulan lalu');
                setHtml('change-pemasukan', getArrowHtml(d.pemasukan_change) + ' vs bln lalu');
                setHtml('change-pengeluaran', getExpenseArrowHtml(d.pengeluaran_change) + ' vs bln lalu');
                setHtml('change-distribusi', getArrowHtml(d.distribusi_change) + ' vs bln lalu');
                setHtml('change-laporan', getArrowHtml(d.laporan_change) + ' vs bln lalu');

                // Update stok menipis warning
                if (d.stok_menipis > 0) {
                    setHtml('stat-stok', '<div style="font-size:12px;color:#ef4444;margin-top:4px;"><i class="fas fa-exclamation-circle"></i> ' + d.stok_menipis + ' item stok menipis</div>');
                } else {
                    setHtml('stat-stok', '<div style="font-size:12px;color:#059669;margin-top:4px;"><i class="fas fa-check-circle"></i> Stok aman</div>');
                }

                // Update charts
                updateCharts(d.charts);

                lastRefresh = new Date();
                updateRefreshIndicator();
            })
            .catch(err => {
                clearTimeout(refreshSkelTimer);
                if (refreshSkelShown) { hideCardSkeleton(); refreshSkelShown = false; }
                console.log('Dashboard refresh failed:', err);
            });
    }

    // Auto-refresh every 60 seconds
    setInterval(refreshDashboard, 60000);
    updateRefreshIndicator();
});
</script>
@endsection

@extends('adminlte::page')

@section('title', 'Slow Queries - SIDUMA PANGAN')

@section('header_actions')
    <form class="form-inline" method="GET">
        <select name="days" class="form-control form-control-sm mr-1" onchange="this.form.submit()">
            @foreach([7, 14, 30, 90] as $d)
            <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} hari terakhir</option>
            @endforeach
        </select>
        <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">Semua Tipe</option>
            <option value="slow" {{ $type == 'slow' ? 'selected' : '' }}>Slow Query</option>
            <option value="high" {{ $type == 'high' ? 'selected' : '' }}>High Query Count</option>
        </select>
    </form>
@endsection

@section('content')
@if(!$logEnabled)
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    Monitoring slow query <strong>nonaktif</strong>. Aktifkan dengan mengatur <code>SLOW_QUERY_LOG=true</code> di <code>.env</code>
    (ambang saat ini: <code>SLOW_QUERY_THRESHOLD_MS={{ $threshold }}</code>, maks query/request: <code>{{ $maxPerRequest }}</code>).
</div>
@endif

{{-- Stat Cards --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-database"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($total) }}</div>
                    <div class="stat-label">Total Entri</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-calendar-day"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($totalToday) }}</div>
                    <div class="stat-label">Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #ef4444;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fee2e2;color:#dc2626;width:40px;height:40px;font-size:16px;"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($slowestMs, 0) }}<small>ms</small></div>
                    <div class="stat-label">Query Terlambat</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fef3c7;color:#d97706;width:40px;height:40px;font-size:16px;"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="stat-value" style="font-size:20px;">{{ number_format($avgMs, 1) }}<small>ms</small></div>
                    <div class="stat-label">Rata-rata</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($total === 0)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-check-circle fa-3x mb-3" style="color:#10b981;"></i>
        <p style="color:#64748b;">Belum ada slow query tercatat dalam periode ini. <br>
        <small>File log: <code>storage/logs/slow-queries-*.log</code></small></p>
    </div>
</div>
@else

{{-- Top Slowest Queries --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-fire mr-2"></i>Query Terlambat Teratas</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Waktu</th><th>Durasi</th><th>SQL</th><th>URL</th></tr>
                </thead>
                <tbody>
                    @forelse($topSlow as $e)
                    <tr>
                        <td style="white-space:nowrap;font-size:12px;">{{ $e['date'] }}<br>{{ $e['time'] }}</td>
                        <td><span class="badge badge-{{ ($e['payload']['time_ms'] ?? 0) >= 1000 ? 'danger' : 'warning' }}">{{ number_format($e['payload']['time_ms'] ?? 0, 1) }} ms</span></td>
                        <td style="font-size:12px;"><code style="white-space:pre-wrap;word-break:break-word;">{{ Str::limit($e['payload']['sql'] ?? '-', 160) }}</code></td>
                        <td style="font-size:12px;max-width:260px;word-break:break-all;">{{ $e['payload']['request']['url'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada slow query</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    {{-- Group by SQL --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-code mr-2"></i>SQL Paling Sering Lambat</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>SQL (dinormalisasi)</th><th>Jumlah</th><th>Maks</th><th>Rata²</th></tr>
                        </thead>
                        <tbody>
                            @forelse($bySql as $row)
                            <tr>
                                <td style="font-size:12px;"><code style="white-space:pre-wrap;word-break:break-word;">{{ Str::limit($row['sql'], 120) }}</code></td>
                                <td><span class="badge badge-info">{{ $row['count'] }}</span></td>
                                <td style="font-size:12px;">{{ number_format($row['max_ms'], 1) }} ms</td>
                                <td style="font-size:12px;">{{ number_format($row['avg_ms'], 1) }} ms</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Group by URL + per day --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-globe mr-2"></i>Per Halaman / URL</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>URL</th><th>Jumlah</th></tr></thead>
                        <tbody>
                            @forelse($byUrl as $row)
                            <tr>
                                <td style="font-size:12px;max-width:280px;word-break:break-all;">{{ $row['url'] }}</td>
                                <td><span class="badge badge-secondary">{{ $row['count'] }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-3 text-muted">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-calendar-alt mr-2"></i>Per Hari</h5>
            </div>
            <div class="card-body p-3">
                @foreach($byDay as $d => $count)
                <div class="d-flex align-items-center mb-1" style="font-size:12px;">
                    <span style="width:110px;">{{ \Carbon\Carbon::parse($d)->translatedFormat('d M Y') }}</span>
                    <div class="progress flex-grow-1 mx-2" style="height:8px;">
                        @php $pct = $count > 0 ? min(100, round($count / max(1, max($byDay)) * 100)) : 0; @endphp
                        <div class="progress-bar {{ $count > 0 ? 'bg-danger' : '' }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="badge {{ $count > 0 ? 'badge-danger' : 'badge-light' }}">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Recent Entries --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-list mr-2"></i>Entri Terbaru ({{ count($recent) }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr><th>Waktu</th><th>Tipe</th><th>Detail</th><th>User</th><th>URL</th></tr>
                </thead>
                <tbody>
                    @forelse($recent as $e)
                    <tr>
                        <td style="white-space:nowrap;font-size:12px;">{{ $e['date'] }} {{ $e['time'] }}</td>
                        <td>
                            @if($e['message'] === 'Slow query')
                            <span class="badge badge-warning">Slow {{ number_format($e['payload']['time_ms'] ?? 0, 1) }}ms</span>
                            @else
                            <span class="badge badge-danger">{{ $e['payload']['queries'] ?? '?' }} query</span>
                            @endif
                        </td>
                        <td style="font-size:12px;">
                            @if($e['message'] === 'Slow query')
                            <code style="white-space:pre-wrap;word-break:break-word;">{{ Str::limit($e['payload']['sql'] ?? '-', 100) }}</code>
                            @else
                            <span>{{ $e['payload']['total_db_ms'] ?? '-' }} ms total DB</span>
                            @endif
                        </td>
                        <td style="font-size:12px;">{{ $e['payload']['request']['user_id'] ?? '-' }}</td>
                        <td style="font-size:12px;max-width:220px;word-break:break-all;">{{ $e['payload']['request']['url'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-3 text-muted">Tidak ada entri</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@extends('adminlte::page')

@section('title', 'Statistik Audit Trail - SIDUMA PANGAN')

@section('header_actions')
    <a href="{{ route('admin.audit-trail.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
    <!-- Summary Stats -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-history"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Aktivitas</span>
                    <span class="info-box-number">{{ number_format($stats['total']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Hari Ini</span>
                    <span class="info-box-number">{{ number_format($stats['today']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-week"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Minggu Ini</span>
                    <span class="info-box-number">{{ number_format($stats['this_week']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bulan Ini</span>
                    <span class="info-box-number">{{ number_format($stats['this_month']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- By Event -->
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-filter mr-2"></i>Berdasarkan Event</h5>
                </div>
                <div class="card-body">
                    <canvas id="eventChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- By Model -->
        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-cube mr-2"></i>Berdasarkan Model</h5>
                </div>
                <div class="card-body">
                    <canvas id="modelChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- By User -->
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Berdasarkan User</h5>
                </div>
                <div class="card-body">
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-clock mr-2"></i>Aktivitas Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($stats['recent'] as $activity)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $activity->causer?->name ?? 'System' }}</strong>
                                        {{ $activity->description }}
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <a href="{{ route('admin.audit-trail.show', $activity) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event Chart
        new Chart(document.getElementById('eventChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($stats['by_event']->toArray())),
                datasets: [{
                    data: @json(array_values($stats['by_event']->toArray())),
                    backgroundColor: [
                        '#28a745', // created
                        '#ffc107', // updated
                        '#dc3545', // deleted
                        '#17a2b8', // other
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Model Chart
        new Chart(document.getElementById('modelChart'), {
            type: 'bar',
            data: {
                labels: @json(array_map(function($item) { return class_basename($item); }, array_keys($stats['by_model']->toArray()))),
                datasets: [{
                    label: 'Aktivitas',
                    data: @json(array_values($stats['by_model']->toArray())),
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // User Chart
        new Chart(document.getElementById('userChart'), {
            type: 'pie',
            data: {
                labels: @json(array_keys($stats['by_user']->toArray())),
                datasets: [{
                    data: @json(array_values($stats['by_user']->toArray())),
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@stop

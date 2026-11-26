@extends('adminlte::page')

@section('title', 'Detail Aktivitas #' . $activity->id . ' - Audit Trail')

@section('header_actions')
    <a href="{{ route('admin.audit-trail.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="row">
        <!-- Activity Info -->
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-info-circle mr-2"></i>Informasi Aktivitas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive"><table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $activity->id }}</td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Event</th>
                            <td>
                                @if($activity->event == 'created')
                                    <span class="badge badge-success"><i class="fas fa-plus"></i> Create</span>
                                @elseif($activity->event == 'updated')
                                    <span class="badge badge-warning"><i class="fas fa-edit"></i> Update</span>
                                @elseif($activity->event == 'deleted')
                                    <span class="badge badge-danger"><i class="fas fa-trash"></i> Delete</span>
                                @else
                                    <span class="badge badge-info">{{ $activity->event }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Log Name</th>
                            <td><code>{{ $activity->log_name ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <th>Batch UUID</th>
                            <td><code>{{ $activity->batch_uuid ?? '-' }}</code></td>
                        </tr>
                    </table></div>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-user mr-2"></i>User</h5>
                </div>
                <div class="card-body">
                    @if($activity->causer)
                        <div class="table-responsive"><table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">ID</th>
                                <td>{{ $activity->causer->id }}</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>{{ $activity->causer->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $activity->causer->email }}</td>
                            </tr>
                        </table></div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Aktivitas oleh System
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Info -->
    @if($activity->subject)
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-cube mr-2"></i>Subject ({{ class_basename($activity->subject_type) }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive"><table class="table table-bordered">
                        <tr>
                            <th style="width: 20%">Model</th>
                            <td><code>{{ $activity->subject_type }}</code></td>
                        </tr>
                        <tr>
                            <th>ID</th>
                            <td>{{ $activity->subject_id }}</td>
                        </tr>
                        <tr>
                            <th>Data</th>
                            <td>
                                <pre class="bg-dark text-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">{{ json_encode($activity->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </td>
                        </tr>
                    </table></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Changes (for updates) -->
    @if($activity->properties && isset($activity->properties['old']))
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Perubahan Data</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive"><table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Nilai Lama</th>
                                <th>Nilai Baru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activity->properties['attributes'] as $field => $newValue)
                                <tr>
                                    <td><strong>{{ $field }}</strong></td>
                                    <td>
                                        <span class="text-danger">
                                            {{ $activity->properties['old'][$field] ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success">
                                            {{ $newValue }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Properties -->
    @if($activity->properties)
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-code mr-2"></i>Properties (JSON)</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Request Info -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h5 class="card-title" style="margin:0;"><i class="fas fa-globe mr-2"></i>Request Info</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive"><table class="table table-bordered">
                        <tr>
                            <th style="width: 20%">IP Address</th>
                            <td><code>{{ $activity->ip_address ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td><code>{{ $activity->user_agent ?? '-' }}</code></td>
                        </tr>
                    </table></div>
                </div>
            </div>
        </div>
    </div>
@stop

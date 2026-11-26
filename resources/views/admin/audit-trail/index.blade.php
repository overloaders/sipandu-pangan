@extends('adminlte::page')

@section('title', 'Audit Trail - SIDUMA PANGAN')

@section('header_actions')
    <a href="{{ route('admin.audit-trail.statistics') }}" class="btn btn-info btn-sm"><i class="fas fa-chart-bar mr-1"></i> Statistik</a>
    <a href="{{ route('admin.audit-trail.export', request()->query()) }}" class="btn btn-success btn-sm"><i class="fas fa-download mr-1"></i> Export CSV</a>
@endsection

@section('content')
    <!-- Filter Section -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title" style="margin:0;"><i class="fas fa-filter mr-2"></i>Filter</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.audit-trail.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>User</label>
                            <select name="user_id" class="form-control">
                                <option value="">Semua User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Event</label>
                            <select name="event" class="form-control">
                                <option value="">Semua Event</option>
                                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Create</option>
                                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Update</option>
                                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Delete</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="card card-outline card-success">
        <div class="card-header">
            <h5 class="card-title" style="margin:0;">
                <i class="fas fa-list"></i> Aktivitas ({{ $activities->total() }} records)
            </h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Model</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                        <th style="width: 100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>{{ $loop->iteration + $activities->firstItem() - 1 }}</td>
                            <td>
                                <small>{{ $activity->created_at->format('d/m/Y') }}</small><br>
                                <span class="text-muted">{{ $activity->created_at->format('H:i:s') }}</span>
                            </td>
                            <td>
                                @if($activity->causer)
                                    <span class="badge badge-primary">{{ $activity->causer->name }}</span>
                                @else
                                    <span class="badge badge-secondary">System</span>
                                @endif
                            </td>
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
                            <td>
                                @if($activity->subject)
                                    <a href="{{ route('admin.audit-trail.model-history', ['subjectType' => $activity->subject_type, 'subjectId' => $activity->subject_id]) }}">
                                        {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><small>{{ Str::limit($activity->description, 50) }}</small></td>
                            <td><code>{{ $activity->ip_address ?? '-' }}</code></td>
                            <td>
                                <a href="{{ route('admin.audit-trail.show', $activity) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted"></i><br>
                                <p class="text-muted mt-2">Tidak ada aktivitas ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $activities->withQueryString()->links() }}
        </div>
    </div>

    <!-- Cleanup Section -->
    <div class="card card-outline card-danger">
        <div class="card-header">
            <h5 class="card-title" style="margin:0;"><i class="fas fa-broom mr-2"></i>Cleanup</h5>
        </div>
        <div class="card-body">
            <form id="delete-audit-cleanup" action="{{ route('admin.audit-trail.cleanup') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Hapus log yang lebih tua dari (hari):</label>
                            <input type="number" name="days" class="form-control" value="365" min="30">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block" onclick="confirmDeleteForm('delete-audit-cleanup')">
                                <i class="fas fa-trash"></i> Bersihkan Log Lama
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Auto-submit filter form on select change
        $('select[name="user_id"], select[name="event"]').change(function() {
            $(this).closest('form').submit();
        });
    });
</script>
@stop

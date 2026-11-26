@extends('adminlte::page')

@section('title', 'Riwayat ' . class_basename($subjectType) . ' #' . $subjectId . ' - Audit Trail')

@section('header_actions')
    <a href="{{ route('admin.audit-trail.model-history-pdf', [$subjectType, $subjectId]) }}" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    <a href="{{ route('admin.audit-trail.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title" style="margin:0;">
                <i class="fas fa-list"></i> Aktivitas untuk {{ class_basename($subjectType) }} #{{ $subjectId }}
                <span class="badge badge-info ml-2">{{ $activities->total() }} records</span>
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
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted"></i><br>
                                <p class="text-muted mt-2">Tidak ada aktivitas untuk model ini</p>
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
@stop

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Riwayat Aktivitas {{ class_basename($subjectType) }} #{{ $subjectId }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#64748b', 'accentDark' => '#475569', 'accentBg' => '#f8fafc', 'accentBorder' => '#e2e8f0', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'RIWAYAT AKTIVITAS MODEL', 'subtitle' => class_basename($subjectType) . ' — ' . $subjectName])

    {{-- RIWAYAT AKTIVITAS --}}
    <div class="section">
        <div class="section-title">Riwayat Aktivitas ({{ $activities->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 18%">Waktu</th>
                    <th style="width: 14%">User</th>
                    <th style="width: 12%">Event</th>
                    <th>Deskripsi</th>
                    <th style="width: 14%">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                <tr>
                    <td>{{ $activity->created_at->format('d M Y H:i:s') }}</td>
                    <td>{{ $activity->causer?->name ?? 'System' }}</td>
                    <td>
                        @php $ev = ['created' => 'badge-success', 'updated' => 'badge-warning', 'deleted' => 'badge-danger']; @endphp
                        <span class="badge {{ $ev[$activity->event] ?? 'badge-info' }}">{{ ucfirst($activity->event) }}</span>
                    </td>
                    <td>{{ $activity->description }}</td>
                    <td><code>{{ $activity->ip_address ?? '-' }}</code></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#94a3b8;">Tidak ada aktivitas untuk model ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

        @include('reports.partials.report-footer')

</body>
</html>

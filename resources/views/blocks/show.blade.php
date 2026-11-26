@extends('adminlte::master')
@section('title', 'Detail Blok: ' . $block->nama_blok)
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-th-large mr-2"></i>Info Blok</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('blocks.destroy', $block) }}','{{ $block->nama_blok }}')" title="Hapus Blok"><i class="fas fa-trash"></i></button>
                    <div>
                        <a href="{{ route('blocks.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="120" style="font-weight:600;color:#64748b;">Kode</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $block->kode_blok }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama</td><td style="font-weight:700;">{{ $block->nama_blok }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jenis</td><td><span class="badge badge-info">{{ ucfirst($block->jenis_blok) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Kapasitas</td><td>{{ $block->terisi }} / {{ $block->kapasitas }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $block->status=='aktif'?'success':'secondary' }}">{{ ucfirst($block->status) }}</span></td></tr>
                </table></div>
                <div style="margin-top:12px;">
                    <h6 style="font-weight:600;font-size:13px;">Isi Blok</h6>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        @php $pct = $block->kapasitas > 0 ? ($block->terisi/$block->kapasitas*100) : 0; @endphp
                        <div class="progress-bar bg-{{ $pct>80?'danger':($pct>50?'warning':'success') }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <small class="text-muted">{{ round($pct) }}% terisi</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-door-open mr-2"></i>Daftar Kamar ({{ $block->rooms->count() }})</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Kode</th><th>Nama</th><th>Kapasitas</th><th>Terisi</th><th>Status</th><th width="80">Aksi</th></tr></thead><tbody>
                @forelse($block->rooms as $r)
                <tr>
                    <td><span class="badge badge-success" style="font-family:monospace;">{{ $r->kode_kamar }}</span></td>
                    <td>{{ $r->nama_kamar }}</td>
                    <td>{{ $r->kapasitas }}</td>
                    <td>{{ $r->terisi }}</td>
                    <td><span class="badge badge-{{ $r->status=='tersedia'?'success':($r->status=='penuh'?'danger':($r->status=='renovasi'?'warning':'secondary')) }}">{{ ucfirst($r->status) }}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <form action="{{ route('rooms.status', [$block, $r]) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Ubah status kamar {{ $r->kode_kamar }}?');">
                                @csrf
                                <select name="status" class="form-control form-control-sm" style="width:auto;display:inline-block;" onchange="this.form.submit()">
                                    @foreach(['tersedia','penuh','renovasi','nonaktif'] as $st)
                                    <option value="{{ $st }}" {{ $r->status==$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <button type="button" class="btn btn-outline-info" onclick="showRoomHistory({{ $r->id }}, '{{ addslashes($r->kode_kamar) }}')" title="Riwayat Status Kamar"><i class="fas fa-history"></i></button>
                            <button class="btn btn-outline-danger" onclick="confirmDelete('/blocks/{{ $block->id }}/rooms/{{ $r->id }}','{{ $r->nama_kamar }}')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty <tr><td colspan="6" class="text-center" style="color:#94a3b8;">Belum ada kamar</td></tr> @endforelse
                </tbody></table></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-plus mr-2"></i>Tambah Kamar</h5></div>
            <div class="card-body">
                <form method="POST" action="/blocks/{{ $block->id }}/rooms" class="form-inline">
                    @csrf
                    <input type="text" name="kode_kamar" class="form-control form-control-sm mr-2" placeholder="Kode Kamar" required style="border-radius:8px;width:120px;">
                    <input type="text" name="nama_kamar" class="form-control form-control-sm mr-2" placeholder="Nama Kamar" required style="border-radius:8px;width:150px;">
                    <input type="number" name="kapasitas" class="form-control form-control-sm mr-2" placeholder="Kapasitas" required min="1" style="border-radius:8px;width:100px;">
                    <button type="submit" class="btn btn-success btn-sm" style="border-radius:8px;"><i class="fas fa-plus"></i></button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status Blok</h5></div>
            <div class="card-body">
                <div class="d-flex flex-wrap" style="gap:6px;">
                    @foreach(['aktif' => 'success', 'renovasi' => 'warning', 'nonaktif' => 'secondary'] as $st => $color)
                        @if($st !== $block->status)
                        <form action="{{ route('blocks.status', $block) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Ubah status blok menjadi {{ ucfirst($st) }}?');">
                            @csrf
                            <input type="hidden" name="status" value="{{ $st }}">
                            <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                        </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status Blok</h5></div>
            <div class="card-body p-0">
                @php $sc = ['aktif' => 'success', 'renovasi' => 'warning', 'nonaktif' => 'secondary']; @endphp
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
                    <tbody>
                    @forelse($block->statusHistories->sortByDesc('created_at') as $h)
                        <tr>
                            <td style="font-size:12px;white-space:nowrap;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($h->status_lama)
                                    <span class="badge badge-{{ $sc[$h->status_lama] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_lama)) }}</span>
                                    <i class="fas fa-arrow-right mx-1" style="font-size:10px;color:#94a3b8;"></i>
                                @endif
                                <span class="badge badge-{{ $sc[$h->status_baru] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_baru)) }}</span>
                            </td>
                            <td style="font-size:12px;">{{ $h->changedBy?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada perubahan status.</td></tr>
                    @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Riwayat Status Kamar --}}
<div class="modal fade" id="roomHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomHistoryModalLabel"><i class="fas fa-history mr-2"></i>Riwayat Status Kamar</h5>
                <div>
                    <a id="roomPdfBtn" href="#" target="_blank" class="btn btn-sm btn-outline-success mr-2" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
                    <tbody id="roomHistoryBody"></tbody>
                </table></div>
            </div>
        </div>
    </div>
</div>

<script>
@php
$roomHistoriesJson = $block->rooms->mapWithKeys(function ($r) {
    return [$r->id => $r->statusHistories->sortByDesc('created_at')->map(function ($h) {
        return [
            'waktu' => $h->created_at->format('d/m/Y H:i'),
            'lama' => $h->status_lama,
            'baru' => $h->status_baru,
            'oleh' => $h->changedBy?->name ?? '-',
        ];
    })->values()];
})->toArray();
@endphp
const roomHistories = {!! json_encode($roomHistoriesJson) !!};
const roomPdfBaseUrl = "{{ url('/reports/rooms') }}";

function roomBadgeColor(s) {
    const m = { 'tersedia': 'success', 'penuh': 'danger', 'renovasi': 'warning', 'nonaktif': 'secondary' };
    return m[s] || 'secondary';
}
function roomCap(s) {
    return String(s).split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
}
function showRoomHistory(roomId, kode) {
    $('#roomPdfBtn').attr('href', roomPdfBaseUrl + '/' + roomId);
    const rows = roomHistories[roomId] || [];
    const tbody = $('#roomHistoryBody');
    tbody.empty();
    if (rows.length === 0) {
        tbody.append('<tr><td colspan="3" class="text-center py-3 text-muted">Belum ada perubahan status.</td></tr>');
    } else {
        rows.forEach(function (h) {
            let lama = '';
            if (h.lama) {
                lama = '<span class="badge badge-' + roomBadgeColor(h.lama) + '">' + roomCap(h.lama) + '</span> <i class="fas fa-arrow-right mx-1" style="font-size:10px;color:#94a3b8;"></i> ';
            }
            tbody.append('<tr><td style="font-size:12px;white-space:nowrap;">' + h.waktu + '</td><td>' + lama + '<span class="badge badge-' + roomBadgeColor(h.baru) + '">' + roomCap(h.baru) + '</span></td><td style="font-size:12px;">' + h.oleh + '</td></tr>');
        });
    }
    $('#roomHistoryModalLabel').text('Riwayat Status Kamar ' + kode);
    $('#roomHistoryModal').modal('show');
}
</script>
@endsection

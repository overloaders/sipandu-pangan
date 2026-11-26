@extends('adminlte::page')

@section('title', 'Detail Indikator Analitik: ' . ($analytic->nama_indikator ?? ''))

@section('header_actions')
    <a href="{{ route('reports.analytics-detail', array_merge([$analytic->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    <a href="{{ route('analytics.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-chart-line mr-2"></i>{{ $analytic->nama_indikator }}</h5>
        <div>
            <a href="{{ route('analytics.edit', $analytic->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="table-responsive"><table class="table table-bordered">
                    <tr><th width="200">Kode Indikator</th><td>{{ $analytic->kode_indikator }}</td></tr>
                    <tr><th>Nama Indikator</th><td>{{ $analytic->nama_indikator }}</td></tr>
                    <tr><th>Kategori</th><td><span class="badge badge-info">{{ ucfirst($analytic->kategori) }}</span></td></tr>
                    <tr><th>Deskripsi</th><td>{{ $analytic->deskripsi ?? '-' }}</td></tr>
                    <tr><th>Rumus Perhitungan</th><td>{{ $analytic->rumus_perhitungan ?? '-' }}</td></tr>
                    <tr><th>Satuan</th><td>{{ $analytic->satuan }}</td></tr>
                    <tr><th>Target Nilai</th><td>{{ number_format($analytic->target_nilai ?? 0, 2) }}</td></tr>
                    <tr><th>Batas Minimum</th><td>{{ $analytic->batas_minimum ?? '-' }}</td></tr>
                    <tr><th>Batas Maksimum</th><td>{{ $analytic->batas_maksimum ?? '-' }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @if($analytic->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Non-aktif</span>
                            @endif
                        </td>
                    </tr>
                </table></div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-bullseye"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Target</span>
                        <span class="info-box-number">{{ number_format($analytic->target_nilai ?? 0, 2) }} {{ $analytic->satuan }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;">Target Periodik</h5>
        <div>
            <a href="{{ route('reports.analytics-detail', $analytic->id) }}" target="_blank" class="btn btn-sm btn-outline-success mr-1" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTarget"><i class="fas fa-plus mr-1"></i> Tambah Target</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Unit</th>
                    <th>Target</th>
                    <th>Realisasi</th>
                    <th>Pencapaian</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analytic->targets as $target)
                <tr>
                    <td>
                        {{ $target->periode_tahun }}
                        @if($target->periode_bulan) / Bulan {{ $target->periode_bulan }} @endif
                        @if($target->periode_triwulan) / TW {{ $target->periode_triwulan }} @endif
                    </td>
                    <td>{{ $target->unit?->nama_unit ?? '-' }}</td>
                    <td>{{ number_format($target->target_nilai ?? 0, 2) }}</td>
                    <td>{{ $target->realisasi_nilai ? number_format($target->realisasi_nilai, 2) : '-' }}</td>
                    <td>
                        @if($target->persentase_capaian)
                            @if($target->persentase_capaian >= 100)
                                <span class="badge badge-success">{{ number_format($target->persentase_capaian, 1) }}%</span>
                            @elseif($target->persentase_capaian >= 75)
                                <span class="badge badge-warning">{{ number_format($target->persentase_capaian, 1) }}%</span>
                            @else
                                <span class="badge badge-danger">{{ number_format($target->persentase_capaian, 1) }}%</span>
                            @endif
                        @else
                            <span class="badge badge-secondary">-</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $target->catatan ? \Illuminate\Support\Str::limit($target->catatan, 40) : '-' }}</td>
                    <td style="white-space:nowrap;">
                        <button type="button" class="btn btn-sm btn-warning"
                            data-toggle="modal" data-target="#modalTarget"
                            data-url="{{ route('analytics.update-target', [$analytic, $target]) }}"
                            data-tahun="{{ $target->periode_tahun }}"
                            data-bulan="{{ $target->periode_bulan ?? '' }}"
                            data-triwulan="{{ $target->periode_triwulan ?? '' }}"
                            data-unit="{{ $target->unit_id }}"
                            data-target="{{ $target->target_nilai }}"
                            data-realisasi="{{ $target->realisasi_nilai }}"
                            data-catatan="{{ $target->catatan }}"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('analytics.destroy-target', [$analytic, $target]) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus target periode {{ $target->periode_tahun }}?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada target periodik — klik "Tambah Target" untuk membuat.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT TARGET --}}
<div class="modal fade" id="modalTarget" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('analytics.store-target', $analytic) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-bullseye"></i> <span id="modalTargetTitle">Tambah Target</span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Tahun *</strong></label>
                        <input type="number" name="periode_tahun" class="form-control" min="2000" max="2100" value="{{ date('Y') }}" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Bulan (opsional)</strong></label>
                        <input type="number" name="periode_bulan" class="form-control" min="1" max="12" placeholder="1-12">
                    </div>
                    <div class="form-group">
                        <label><strong>Triwulan (opsional)</strong></label>
                        <input type="number" name="periode_triwulan" class="form-control" min="1" max="4" placeholder="1-4">
                    </div>
                    @if(auth()->user()->hasRole('super-admin'))
                    <div class="form-group">
                        <label><strong>Unit</strong></label>
                        <select name="unit_id" class="form-control">
                            @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ $u->id == $analytic->unit_id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <label><strong>Target Nilai *</strong></label>
                        <input type="number" name="target_nilai" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Realisasi Nilai</strong></label>
                        <input type="number" name="realisasi_nilai" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label><strong>Catatan</strong></label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
$('#modalTarget').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    if (btn.data('url')) {
        // Edit mode
        form.attr('action', btn.data('url'));
        form.find('input[name=_method]').remove();
        form.append('<input type="hidden" name="_method" value="PUT">');
        $('#modalTargetTitle').text('Edit Target');
        form.find('[name=periode_tahun]').val(btn.data('tahun'));
        form.find('[name=periode_bulan]').val(btn.data('bulan') || '');
        form.find('[name=periode_triwulan]').val(btn.data('triwulan') || '');
        form.find('[name=unit_id]').val(btn.data('unit'));
        form.find('[name=target_nilai]').val(btn.data('target'));
        form.find('[name=realisasi_nilai]').val(btn.data('realisasi'));
        form.find('[name=catatan]').val(btn.data('catatan') || '');
    } else {
        // Add mode
        form.attr('action', "{{ route('analytics.store-target', $analytic) }}");
        form.find('input[name=_method]').remove();
        $('#modalTargetTitle').text('Tambah Target');
        form.find('[name=periode_tahun]').val("{{ date('Y') }}");
        form.find('[name=periode_bulan]').val('');
        form.find('[name=periode_triwulan]').val('');
        form.find('[name=unit_id]').val("{{ $analytic->unit_id }}");
        form.find('[name=target_nilai]').val('');
        form.find('[name=realisasi_nilai]').val(0);
        form.find('[name=catatan]').val('');
    }
});
</script>
@endsection

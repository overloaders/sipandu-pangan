@extends('adminlte::page')
@section('title', 'Peserta: ' . ($program->nama_program ?? ''))
@section('header_actions')
    <a href="{{ route('training.show', $program) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Tambah Peserta</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('training.add-participant', $program) }}" method="POST" class="form-inline">
            @csrf
            <select name="wbp_id" class="form-control form-control-sm mr-2" required>
                <option value="">-- Pilih WBP --</option>
                @foreach($wbps as $w)<option value="{{ $w->id }}">{{ $w->nomor_induk }} - {{ $w->nama_lengkap }}</option>@endforeach
            </select>

            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">                    <thead><tr><th>#</th><th>Nomor Induk</th><th>Nama</th><th>Status</th><th>Nilai</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($program->participants as $i => $p)
                @php $hasCert = $p->certification !== null; @endphp
                <tr style="{{ $hasCert ? 'background: #f0fdf4;' : '' }}">
                    <td>{{ $i+1 }}</td>
                    <td><code>{{ $p->wbp->nomor_induk ?? '-' }}</code></td>
                    <td>
                        {{ $p->wbp->nama_lengkap ?? '-' }}
                        @if($hasCert)
                            <span class="badge badge-success ml-1" title="Sudah terbit sertifikat"><i class="fas fa-check-circle"></i> Bersertifikat</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $p->status=='selesai'?'success':($p->status=='hadir'?'info':($p->status=='lulus'?'success':'secondary')) }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td>
                        <form action="{{ route('training.update-nilai', [$program, $p]) }}" method="POST" class="form-inline" style="display:flex;gap:4px;align-items:center;">
                            @csrf @method('PUT')
                            <input type="number" name="nilai_akhir" value="{{ $p->nilai_akhir ?? '' }}" min="0" max="100" step="0.01" class="form-control form-control-sm" style="width:70px" placeholder="0-100">
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Simpan Nilai"><i class="fas fa-save"></i></button>
                        </form>
                    </td>
                    <td class="text-nowrap">
                        @if($hasCert)
                            <a href="{{ route('training.print-certificate', [$program, $p->certification]) }}" target="_blank" class="btn btn-sm btn-success" title="Cetak Sertifikat"><i class="fas fa-print"></i> Cetak</a>
                        @elseif($program->status == 'selesai')
                            <button type="button" class="btn btn-sm btn-warning" title="Terbitkan Sertifikat"
                                onclick="swalConfirmPost('{{ route('training.issue-participant-cert', [$program, $p]) }}', 'Terbitkan Sertifikat?', 'Sertifikat akan diterbitkan untuk {{ addslashes($p->wbp->nama_lengkap ?? '') }}.', '#f59e0b', 'Ya, Terbitkan')">
                                <i class="fas fa-certificate"></i> Terbitkan
                            </button>
                        @endif
                        <form action="{{ route('training.remove-participant', [$program, $p]) }}" method="POST" class="d-inline ml-1">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteForm(this.closest('form'))" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty <tr><td colspan="6" class="text-center text-muted py-3">Belum ada peserta</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@extends('adminlte::page')
@section('title', 'Sertifikat: ' . ($program->nama_pelatihan ?? ''))
@section('header_actions')
    <a href="{{ route('training.participants', $program) }}" class="btn btn-info btn-sm"><i class="fas fa-users mr-1"></i> Kelola Peserta</a>
    <a href="{{ route('training.show', $program) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title m-0"><i class="fas fa-certificate mr-1"></i> Daftar Sertifikat Terbitan ({{ $program->certifications->count() }})</h5>
        <div class="card-tools">
            <small class="text-muted">Sertifikat diterbitkan dari halaman Peserta Pelatihan</small>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Sertifikat</th>
                    <th>Nama Peserta</th>
                    <th>Nomor Induk</th>
                    <th>Nama Sertifikat</th>
                    <th>Tgl Terbit</th>
                    <th>Tingkat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($program->certifications as $i => $c)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><code>{{ $c->kode_sertifikat }}</code></td>
                    <td><strong>{{ $c->wbp->nama_lengkap ?? '-' }}</strong></td>
                    <td><small class="text-muted">{{ $c->wbp->nomor_induk ?? '-' }}</small></td>
                    <td>{{ $c->nama_sertifikat }}</td>
                    <td>{{ $c->tanggal_terbit ? $c->tanggal_terbit->format('d/m/Y') : '-' }}</td>
                    <td>{{ ucfirst($c->tingkat ?? '-') }} <span class="badge badge-{{ $c->status=='aktif'?'success':'secondary' }}">{{ ucfirst($c->status ?? '-') }}</span></td>
                    <td>
                        <a href="{{ route('training.print-certificate', [$program, $c]) }}" target="_blank" class="btn btn-sm btn-success" title="Cetak Sertifikat">
                            <i class="fas fa-print"></i> Cetak
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-certificate fa-2x mb-2 d-block text-muted"></i>
                        Belum ada sertifikat yang diterbitkan.<br>
                        <a href="{{ route('training.participants', $program) }}" class="btn btn-sm btn-primary mt-2"><i class="fas fa-users"></i> Kelola Peserta</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

{{--
    Kop surat resmi untuk semua laporan PDF (DomPDF-safe).

    Penggunaan:
    @include('reports.partials.letterhead', [
        'title'    => 'LAPORAN KEUANGAN',          // judul laporan (wajib)
        'subtitle' => 'Periode ... s/d ...',       // opsional: periode / keterangan
    ])

    Data unit otomatis diambil dari variabel $unit (objek App\Models\Unit)
    yang di-inject oleh ReportController::renderOrDownload.
    Jika $unit null (mis. super-admin tanpa filter unit), tampil 'SIDUMA PANGAN'.
--}}
@php
    $kopUnit = $unit ?? null;
    $kopTitle = $title ?? '';
    $kopSubtitle = $subtitle ?? null;

    // Nama instansi: nama unit jika ada, fallback SIDUMA PANGAN
    $kopInstansi = $kopUnit?->nama_unit ? strtoupper($kopUnit->nama_unit) : 'SIDUMA PANGAN';

    // Alamat & kontak unit (diisi manual di data master unit)
    $kopAlamat = trim(collect([$kopUnit?->alamat, $kopUnit?->kota_kab, $kopUnit?->provinsi])->filter()->implode(', '));
    $kopKontak = trim(collect([
        $kopUnit?->telepon ? 'Telp: ' . $kopUnit->telepon : null,
        $kopUnit?->email ? 'Email: ' . $kopUnit->email : null,
    ])->filter()->implode('  |  '));

    // Subtitle otomatis: periode filter jika ada, selain itu tanggal cetak
    if (!$kopSubtitle) {
        if (!empty($fromDate ?? null) && !empty($toDate ?? null)) {
            $kopSubtitle = 'Per ' . \Carbon\Carbon::parse($fromDate)->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($toDate)->translatedFormat('d F Y');
        } elseif (!empty($fromDate ?? null)) {
            $kopSubtitle = 'Dari ' . \Carbon\Carbon::parse($fromDate)->translatedFormat('d F Y');
        } elseif (!empty($toDate ?? null)) {
            $kopSubtitle = 'Sampai ' . \Carbon\Carbon::parse($toDate)->translatedFormat('d F Y');
        } else {
            $kopSubtitle = 'Dicetak: ' . now()->format('d M Y H:i');
        }
    }

    // Logo: embed base64 (DomPDF tidak bisa akses file luar chroot, data URI selalu aman)
    $kopLogoUri = null;
    if ($kopUnit && $kopUnit->logo_path) {
        $kopLogoUri = 'data:image/png;base64,' . base64_encode(file_get_contents($kopUnit->logo_path));
    }
@endphp
<div class="kop">
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if($kopLogoUri)
                    <img src="{{ $kopLogoUri }}" class="kop-logo-img" alt="Logo">
                @endif
            </td>
            <td class="kop-text-cell">
                <div class="kop-instansi">{{ $kopInstansi }}</div>
                @if($kopAlamat)
                    <div class="kop-alamat">{{ $kopAlamat }}</div>
                @endif
                @if($kopKontak)
                    <div class="kop-kontak">{{ $kopKontak }}</div>
                @endif
            </td>
        </tr>
    </table>
    <div class="kop-garis-tebal"></div>
    <div class="kop-garis-tipis"></div>
    <div class="kop-judul">
        <h1>{{ $kopTitle }}</h1>
        @if($kopSubtitle)
            <div class="kop-subtitle">{{ $kopSubtitle }}</div>
        @endif
        <div class="kop-unit">Unit: {{ $kopUnit?->nama_unit ?? 'Semua Unit' }}</div>
    </div>
</div>

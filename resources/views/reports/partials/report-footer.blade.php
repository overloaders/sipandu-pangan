{{--
    Footer bersama untuk semua laporan PDF (DomPDF-safe).
    Menampilkan nama unit + tanggal cetak + nomor halaman otomatis.

    Penggunaan:
    @include('reports.partials.report-footer')
--}}
<div class="report-footer">
    <span>{{ $unit?->nama_unit ?? 'SIDUMA PANGAN' }} — Dokumen dicetak secara otomatis pada {{ now()->format('d/m/Y H:i') }}</span>
    <span class="report-footer-page">Halaman <span class="pagenum"></span></span>
</div>

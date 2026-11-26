<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $certificate->kode_sertifikat }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        .certificate {
            width: 800px;
            height: 566px; /* 297mm × √2 ratio for A4 landscape */
            background: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        /* Decorative border */
        .certificate::before {
            content: '';
            position: absolute;
            top: 12px; left: 12px; right: 12px; bottom: 12px;
            border: 3px solid #2e7d32;
            border-radius: 4px;
        }
        
        .certificate::after {
            content: '';
            position: absolute;
            top: 18px; left: 18px; right: 18px; bottom: 18px;
            border: 1px solid #81c784;
            border-radius: 2px;
        }
        
        /* Corner decorations */
        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: #2e7d32;
        }
        .corner-tl { top: 20px; left: 20px; border-top: 3px solid; border-left: 3px solid; }
        .corner-tr { top: 20px; right: 20px; border-top: 3px solid; border-right: 3px solid; }
        .corner-bl { bottom: 20px; left: 20px; border-bottom: 3px solid; border-left: 3px solid; }
        .corner-br { bottom: 20px; right: 20px; border-bottom: 3px solid; border-right: 3px solid; }
        
        .content {
            position: relative;
            z-index: 1;
            padding: 50px 60px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .header {
            text-align: center;
        }
        
        .header .logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 8px;
        }
        
        .header .logo-area .icon {
            font-size: 36px;
            color: #2e7d32;
        }
        
        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #1b5e20;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        .header .subtitle {
            font-size: 12px;
            color: #666;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        .divider {
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #2e7d32, transparent);
            margin: 12px auto;
        }
        
        .body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
        }
        
        .body .to {
            font-size: 13px;
            color: #555;
        }
        
        .body .name {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #1b5e20;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 6px;
        }
        
        .body .nip {
            font-size: 12px;
            color: #888;
            margin-top: -4px;
        }
        
        .body .detail {
            font-size: 13px;
            color: #444;
            line-height: 1.8;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .body .detail strong {
            color: #2e7d32;
        }
        
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 20px;
        }
        
        .footer .left {
            text-align: left;
            font-size: 11px;
            color: #888;
        }
        
        .footer .right {
            text-align: right;
            font-size: 11px;
            color: #888;
        }
        
        .footer .date {
            margin-bottom: 50px;
        }
        
        .footer .signature-name {
            font-weight: 600;
            color: #333;
            font-size: 12px;
        }
        
        .footer .signature-title {
            font-size: 10px;
            color: #888;
        }
        
        .qr-area {
            position: absolute;
            bottom: 40px;
            right: 50px;
            width: 70px;
            height: 70px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #aaa;
            background: #fafafa;
        }
        
        .stamp {
            position: absolute;
            bottom: 45px;
            right: 140px;
            width: 80px;
            height: 80px;
            border: 2px dashed #c62828;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #c62828;
            opacity: 0.5;
            transform: rotate(-15deg);
        }
        
        @media print {
            body { background: none; }
            .certificate { box-shadow: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
@php
    $certUnit = $program->unit ?? $certificate->trainingProgram->unit ?? null;
    $certKota = trim($certUnit->kota_kab ?? '');
    $certLogo = $certUnit?->logo_url ?? null;
@endphp
<body>
    <div class="no-print" style="position:fixed;top:20px;right:20px;z-index:999;">
        <button onclick="window.print()" style="background:#2e7d32;color:#fff;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-size:14px;">
            🖨️ Cetak Sertifikat
        </button>
        <button onclick="window.close()" style="background:#666;color:#fff;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-size:14px;margin-left:8px;">
            ✕ Tutup
        </button>
    </div>
    
    <div class="certificate">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>
        
        <div class="content">
            <div class="header">
                <div class="logo-area">
                    @if($certLogo)
                        <img src="{{ $certLogo }}" style="height:52px;width:auto;object-fit:contain;" alt="Logo">
                    @else
                        <span class="icon">🎓</span>
                    @endif
                    <div>
                        <h1>Sertifikat</h1>
                        <div class="subtitle">Pendidikan & Pelatihan Keterampilan</div>
                        @if($certUnit?->nama_unit)
                            <div class="subtitle" style="letter-spacing:2px;margin-top:4px;color:#2e7d32;">{{ strtoupper($certUnit->nama_unit) }}</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="body">
                <div class="to">Diberikan kepada:</div>
                <div class="name">{{ $wbp->nama_lengkap ?? '-' }}</div>
                <div class="nip">NIP: {{ $wbp->nomor_induk ?? '-' }}</div>
                
                <div class="detail">
                    Telah berhasil mengikuti program <strong>{{ $program->nama_pelatihan }}</strong>
                    dengan kategori <strong>{{ ucfirst($program->kategori) }}</strong>
                    @if($program->tanggal_mulai && $program->tanggal_selesai)
                        yang dilaksanakan pada tanggal
                        <strong>{{ \Carbon\Carbon::parse($program->tanggal_mulai)->format('d M Y') }}</strong>
                        s/d <strong>{{ \Carbon\Carbon::parse($program->tanggal_selesai)->format('d M Y') }}</strong>
                    @endif
                    @if($program->jam_pelatihan)
                        dengan total durasi <strong>{{ $program->jam_pelatihan }} jam</strong>
                    @endif
                    @if($program->lokasi)
                        di <strong>{{ $program->lokasi }}</strong>
                    @endif.
                </div>
            </div>
            
            <div class="footer">
                <div class="left">
                    <div class="date">{{ $certKota ? $certKota.', ' : '' }}{{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('d M Y') }}</div>
                    <div class="signature-name">_________________________</div>
                    <div class="signature-title">Kepala Unit / Instruktur</div>
                </div>
                <div class="right">
                    <div class="date">{{ $certKota ? $certKota.', ' : '' }}{{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('d M Y') }}</div>
                    <div class="signature-name">_________________________</div>
                    <div class="signature-title">Kepala Lembaga Pemasyarakatan</div>
                </div>
            </div>
        </div>
        
        <div class="stamp">STAMPIL</div>
        <div class="qr-area">QR CODE</div>
    </div>
</body>
</html>

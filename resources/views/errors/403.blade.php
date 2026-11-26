<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - SIDUMA PANGAN</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-page { text-align: center; padding: 40px; }
        .error-code { font-size: 120px; font-weight: 800; color: #e2e8f0; line-height: 1; }
        .error-icon { font-size: 60px; color: #ef4444; margin: 20px 0; }
        .error-title { font-size: 24px; font-weight: 700; color: #1e293b; margin: 10px 0; }
        .error-desc { font-size: 16px; color: #64748b; margin: 10px 0 30px; }
        .btn-back { background: #10b981; color: #fff; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s; }
        .btn-back:hover { background: #059669; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-code">403</div>
        <div class="error-icon"><i class="fas fa-shield-alt"></i></div>
        <div class="error-title">Akses Ditolak</div>
        <div class="error-desc">Anda tidak memiliki izin untuk mengakses halaman ini.<br>Mohon hubungi administrator jika Anda membutuhkan akses.</div>
        <a href="{{ url('/dashboard') }}" class="btn-back">
            <i class="fas fa-home mr-2"></i>Kembali ke Dashboard
        </a>
    </div>
</body>
</html>

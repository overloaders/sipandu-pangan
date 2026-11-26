<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — SIDUMA PANGAN</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            overflow: hidden;
        }

        /* Left decorative panel */
        .login-branding {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        .login-branding::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 30% 30%, rgba(16,185,129,0.15) 0%, transparent 50%),
                radial-gradient(circle at 70% 70%, rgba(6,182,212,0.1) 0%, transparent 50%);
        }

        .login-branding .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
        }

        .login-branding .brand-icon {
            width: 80px;
            height: 80px;
            background: rgba(16,185,129,0.15);
            border: 2px solid rgba(16,185,129,0.3);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .login-branding .brand-icon i {
            font-size: 36px;
            color: #10b981;
        }

        .login-branding h1 {
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .login-branding h1 span { color: #10b981; }

        .login-branding p {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
        }

        .login-branding .features {
            margin-top: 40px;
            text-align: left;
        }

        .login-branding .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            color: #cbd5e1;
            font-size: 13px;
        }

        .login-branding .feature-item i {
            color: #10b981;
            font-size: 14px;
            width: 20px;
            text-align: center;
        }

        /* Right login form */
        .login-form-wrapper {
            width: 480px;
            min-width: 480px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            position: relative;
        }

        .login-form-wrapper .form-logo {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-form-wrapper .form-logo .icon-circle {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(16,185,129,0.3);
        }

        .login-form-wrapper .form-logo .icon-circle i {
            color: #fff;
            font-size: 24px;
        }

        .login-form-wrapper h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .login-form-wrapper .subtitle {
            color: #64748b;
            font-size: 13px;
            margin-top: 6px;
        }

        .login-form {
            width: 100%;
            max-width: 360px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #10b981;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }

        .form-group input::placeholder { color: #a0aec0; }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #64748b;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #10b981;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(16,185,129,0.3);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16,185,129,0.4);
        }

        .btn-login:active { transform: translateY(0); }

        .error-msg {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .error-msg ul { margin: 0; padding-left: 16px; }
        .error-msg li { margin-bottom: 2px; }

        .manual-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 18px;
        }

        .manual-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }

        .manual-link:hover {
            border-color: #10b981;
            color: #059669;
            background: #ecfdf5;
        }

        .manual-link i { color: #10b981; }

        .form-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        .form-footer a { color: #64748b; text-decoration: underline; }
        .form-footer a:hover { color: #059669; }

        /* Responsive */
        @media (max-width: 900px) {
            .login-branding { display: none; }
            .login-form-wrapper { width: 100%; min-width: unset; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <!-- Left Branding Panel -->
    <div class="login-branding">
        <div class="brand-content">
            <div class="brand-icon">
                <i class="fas fa-seedling"></i>
            </div>
            <h1>SIDUMA <span>PANGAN</span></h1>
            <p>Sistem Informasi Terpadu Pemasyarakatan<br>dan Ketahanan Pangan</p>

            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Manajemen program pertanian & peternakan</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Monitoring real-time produksi pangan</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Laporan keuangan & analitik otomatis</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Integrasi data warga binaan pemasyarakatan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Login Form -->
    <div class="login-form-wrapper">
        <div class="form-logo">
            <div class="icon-circle">
                <i class="fas fa-seedling"></i>
            </div>
            <h2>Selamat Datang</h2>
            <p class="subtitle">Sistem Informasi Terpadu Pemasyarakatan<br>dan Ketahanan Pangan</p>
        </div>

        @if($errors->any())
        <div class="error-msg" style="width:100%;max-width:360px;">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form class="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Masukkan email anda"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password anda" required>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Masuk
            </button>
        </form>

        <div class="manual-actions">
            <a href="{{ route('manual') }}" class="manual-link">
                <i class="fas fa-book-open"></i>
                Baca Manual Book
            </a>
            <a href="{{ asset('manual-assets/SIDUMA-PANGAN-Manual-Book.pdf') }}" class="manual-link" download>
                <i class="fas fa-file-pdf"></i>
                Unduh PDF
            </a>
        </div>

        <div class="form-footer">
            &copy; {{ date('Y') }} SIDUMA PANGAN &middot;
            <a href="{{ route('manual') }}">Panduan Pengguna</a>
        </div>
    </div>
</body>
</html>

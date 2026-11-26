<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIDUMA PANGAN') }} - @yield('title', 'Dashboard')</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- AdminLTE CSS --}}
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary-green: #10b981;
            --primary-green-light: #d1fae5;
            --primary-teal: #0d9488;
            --primary-blue: #3b82f6;
            --primary-purple: #8b5cf6;
            --accent-pink: #ec4899;
            --accent-yellow: #f59e0b;
            --accent-orange: #f97316;
            --accent-red: #ef4444;
            --accent-cyan: #06b6d4;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-light: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --sidebar-bg: linear-gradient(180deg, #e8f5e9 0%, #c8e6c9 50%, #a5d6a7 100%);
            --sidebar-hover: rgba(46, 125, 50, 0.10);
            --sidebar-active: rgba(46, 125, 50, 0.15);
            --sidebar-active-border: #2e7d32;
        }

        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            font-size: 14px;
        }

        /* Sidebar */
        .main-sidebar {
            background: var(--sidebar-bg) !important;
            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .main-sidebar .brand-link {
            border-bottom: 1px solid rgba(46,125,50,0.15);
            padding: 14px 16px;
            background: rgba(255,255,255,0.25);
        }

        .main-sidebar .brand-img {
            border: 2px solid rgba(46,125,50,0.3);
        }

        .main-sidebar .brand-text {
            font-weight: 700;
            font-size: 16px;
            color: #1b5e20 !important;
            letter-spacing: 0.02em;
        }

        .main-sidebar .brand-text b { color: #2e7d32; }

        .nav-sidebar > .nav-item > .nav-link {
            color: rgba(27, 94, 32, 0.85);
            padding: 10px 16px;
            margin: 2px 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            letter-spacing: 0.01em;
        }

        .nav-sidebar > .nav-item > .nav-link:hover {
            color: #1b5e20;
            background: var(--sidebar-hover);
        }

        .nav-sidebar > .nav-item > .nav-link.active {
            background: var(--sidebar-active);
            color: #1b5e20;
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--sidebar-active-border);
        }

        .nav-sidebar > .nav-item > .nav-link.active .nav-icon {
            color: #2e7d32 !important;
        }

        .nav-sidebar .nav-header {
            color: rgba(27, 94, 32, 0.55);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 18px 16px 8px;
        }

        .nav-sidebar .nav-treeview > .nav-item > .nav-link {
            padding-left: 44px;
            font-size: 12.5px;
            color: rgba(27, 94, 32, 0.75);
        }

        .nav-sidebar .nav-treeview > .nav-item > .nav-link:hover {
            color: #1b5e20;
        }

        .nav-sidebar .nav-treeview > .nav-item > .nav-link.active {
            color: #6ee7b7;
        }

        /* Top Navbar */
        .main-header.navbar {
            background: #fff;
            border-bottom: 1px solid var(--border-light);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .main-header .nav-link {
            color: var(--text-secondary) !important;
        }

        /* Content */
        .content-wrapper {
            background: var(--bg-main) !important;
        }

        /* Stat Cards */
        .stat-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-light);
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transform: translateY(-1px);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-card .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .stat-card .stat-change {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .stat-change.up { background: #d1fae5; color: #059669; }
        .stat-change.down { background: #fee2e2; color: #dc2626; }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .card-header {
            background: transparent !important;
            border-bottom: 1px solid var(--border-light);
            padding: 16px 20px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Tables */
        .table thead th {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-light);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 12px 16px;
        }

        .table td {
            padding: 12px 16px;
            vertical-align: middle;
            border-color: var(--border-light);
            font-size: 13px;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* Buttons */
        .btn { border-radius: 8px; font-size: 13px; font-weight: 500; }
        .btn-success { background: var(--primary-green); border-color: var(--primary-green); }
        .btn-success:hover { background: #059669; border-color: #059669; }
        .btn-primary { background: var(--primary-blue); border-color: var(--primary-blue); }
        .btn-teal { background: var(--primary-teal); border-color: var(--primary-teal); color: #fff; }
        .btn-outline-success { color: var(--primary-green); border-color: var(--primary-green); }
        .btn-outline-success:hover { background: var(--primary-green); color: #fff; }

        /* Badges */
        .badge { font-weight: 500; font-size: 11px; padding: 4px 10px; border-radius: 6px; }
        .badge-success { background: #d1fae5; color: #059669; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-secondary { background: #f1f5f9; color: #475569; }

        /* Flow/Chevron Cards (Kledo style) */
        .flow-card {
            border-radius: 12px;
            padding: 24px;
            color: #fff;
            text-align: center;
            position: relative;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .flow-card i { font-size: 28px; margin-bottom: 8px; opacity: 0.9; }
        .flow-card .flow-value { font-size: 28px; font-weight: 700; }
        .flow-card .flow-label { font-size: 12px; opacity: 0.9; margin-top: 4px; }

        /* Clickable flow cards */
        .flow-card-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .flow-card-link:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,.18);
            text-decoration: none;
        }
        .flow-card-link.active {
            outline: 3px solid #059669;
            outline-offset: -3px;
            box-shadow: 0 0 0 4px rgba(5,150,105,.25);
        }
        .flow-card-link .flow-count {
            font-size: 13px;
            font-weight: 600;
            background: rgba(255,255,255,.25);
            border-radius: 999px;
            padding: 2px 12px;
            margin-top: 6px;
        }

        /* Pagination */
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid var(--border-light);
            font-size: 13px;
            line-height: 1.4;
            color: var(--text-secondary);
            min-width: 30px;
            text-align: center;
        }
        .pagination .page-link:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }
        .pagination .page-item.active .page-link {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: #ffffff;
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.45;
            color: var(--text-secondary);
        }

        /* Breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 13px;
        }

        .breadcrumb-item a { color: var(--primary-green); text-decoration: none; }
        .breadcrumb-item.active { color: var(--text-secondary); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Login */
        .login-box { width: 400px; }
        .login-card-body { border-radius: 16px; padding: 32px; }
        .login-box-msg { color: var(--text-secondary); font-size: 14px; }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-card .stat-value { font-size: 20px; }

            /* Filter bar flex containers - stack vertically on mobile */
            .filter-header .d-flex {
                flex-wrap: wrap !important;
                gap: 8px;
            }

            /* Title + filter row: stack on mobile */
            .filter-header > .col-12 > .d-flex {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px;
            }

            /* Filter control groups inside flex */
            .filter-header .d-flex.align-items-center {
                width: 100% !important;
                flex-wrap: wrap !important;
                gap: 8px;
            }

            /* Date inputs full width on mobile */
            .filter-header input[type="date"],
            .filter-header select {
                width: 100% !important;
                min-width: 0 !important;
            }

            /* Labels for date inputs */
            .filter-header label {
                font-size: 11px;
                margin-bottom: 2px;
            }

            /* Buttons: full width or wrap nicely */
            .filter-header .btn-group {
                width: 100% !important;
                flex-wrap: wrap !important;
            }

            .filter-header .btn-group .btn {
                flex: 1 1 auto;
                min-width: 0;
                white-space: nowrap;
            }

            /* Card headers with filter controls */
            .card-header .d-flex {
                flex-wrap: wrap !important;
                gap: 8px;
            }

            .card-header .d-flex.align-items-center {
                width: 100% !important;
            }

            .card-header select {
                max-width: 100% !important;
                width: 100% !important;
            }

            /* form-inline in card headers: stack on mobile */
            .card-header .form-inline {
                flex-wrap: wrap !important;
                gap: 4px;
                width: 100% !important;
            }

            .card-header .form-inline .form-control {
                width: 100% !important;
                min-width: 0 !important;
            }

            /* Footer pagination */
            .card-footer.d-flex {
                flex-wrap: wrap !important;
                gap: 8px;
                justify-content: center !important;
            }

            .card-footer .text-muted {
                width: 100%;
                text-align: center;
            }

            /* Fixed-width inputs in card headers: full width */
            .card-header input[type="text"],
            .card-header input[type="search"] {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }

            /* Input-group in card headers */
            .card-header .input-group {
                width: 100% !important;
            }

            /* Prevent body horizontal overflow */
            body {
                overflow-x: hidden;
            }

            .content-wrapper {
                overflow-x: hidden;
            }

            /* Quick date preset buttons */
            .btn-group-sm .btn {
                font-size: 11px;
                padding: 4px 8px;
            }

            /* Filter form grid columns */
            .filter-header .row .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            /* Table responsive fix */
            .table-responsive {
                border: none;
            }

            /* Date input row: two columns */
            .filter-date-row {
                display: flex;
                gap: 8px;
            }
            .filter-date-row > div {
                flex: 1;
            }
        }

        /* === Scrollbar tabel (horizontal, terlihat) === */
        .table-responsive {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #eef2f7;
        }
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #eef2f7;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        .table-responsive.has-scroll {
            box-shadow: inset -14px 0 10px -10px rgba(15, 23, 42, 0.14);
        }
        @media (max-width: 767.98px) {
            .table-responsive {
                scrollbar-width: thin;
                scrollbar-color: #94a3b8 #eef2f7;
            }
            .table-responsive::-webkit-scrollbar {
                height: 10px;
            }
        }

        /* === Scroll effects (site-wide) === */
        .dash-js .dash-row {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .45s cubic-bezier(.16, 1, .3, 1), transform .45s cubic-bezier(.16, 1, .3, 1);
            will-change: opacity, transform;
        }
        .dash-js .dash-row.visible {
            opacity: 1;
            transform: none;
        }
        @media (prefers-reduced-motion: reduce) {
            .dash-js .dash-row {
                opacity: 1;
                transform: none;
                transition: none;
                will-change: auto;
            }
        }
        .filter-header {
            position: sticky;
            top: 57px;
            z-index: 1020;
            background: rgba(248, 250, 252, .92);
            backdrop-filter: blur(8px);
            padding: 10px 12px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        }
        @media (max-width: 767.98px) {
            .filter-header {
                position: static !important;
                box-shadow: none;
            }
        }
        #btn-back-top {
            position: fixed;
            right: 22px;
            bottom: 22px;
            z-index: 1040;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #2563eb;
            color: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .35);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity .25s ease, transform .25s ease, visibility .25s;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #btn-back-top.show {
            opacity: 1;
            visibility: visible;
            transform: none;
        }
        #btn-back-top:hover {
            background: #1d4ed8;
        }
    </style>
    @yield('extra_css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

@auth
    {{-- Navbar --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link text-muted">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="badge badge-success mr-1">{{ auth()->user()->name ?? 'Guest' }}</span>
                    <i class="fas fa-user-circle"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="/profile" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profil
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
@endauth

@auth
    {{-- Sidebar --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-2">
        <a href="/" class="brand-link text-center" style="text-decoration:none">
            <span class="brand-text"><i class="fas fa-seedling mr-1" style="color:#10b981"></i> <b>SIDUMA</b> PANGAN</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    {{-- DASHBOARD --}}
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    {{-- MODUL SDP --}}
                    <li class="nav-header">MODUL SDP</li>
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor', 'operator']))
                    <li class="nav-item">
                        <a href="/wbp" class="nav-link {{ request()->is('wbp*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Registrasi WBP</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor']))
                    <li class="nav-item">
                        <a href="/blocks" class="nav-link {{ request()->is('blocks*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-th-large"></i>
                            <p>Blok & Kamar</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor', 'operator']))
                    <li class="nav-item">
                        <a href="/daily-reports" class="nav-link {{ request()->is('daily-reports*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Laporan Harian</p>
                        </a>
                    </li>
                    @endif

                    {{-- MODUL KETAHANAN PANGAN --}}
                    <li class="nav-header">KETAHANAN PANGAN</li>
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor']))
                    <li class="nav-item">
                        <a href="/food-programs" class="nav-link {{ request()->is('food-programs*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-seedling"></i>
                            <p>Program Pangan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/land-plots" class="nav-link {{ request()->is('land-plots*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-map"></i>
                            <p>Manajemen Lahan</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor', 'operator']))
                    <li class="nav-item">
                        <a href="/farming-plans" class="nav-link {{ request()->is('farming-plans*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tractor"></i>
                            <p>Pertanian</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/harvests" class="nav-link {{ request()->is('harvests*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-apple-alt"></i>
                            <p>Hasil Panen</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/livestock-barns" class="nav-link {{ request()->is('livestock-barns*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-paw"></i>
                            <p>Peternakan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/fish-ponds" class="nav-link {{ request()->is('fish-ponds*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-fish"></i>
                            <p>Perikanan</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor']))
                    <li class="nav-item">
                        <a href="/nutrition" class="nav-link {{ request()->is('nutrition*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-utensils"></i>
                            <p>Nutrisi & Menu</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/processing" class="nav-link {{ request()->is('processing*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-industry"></i>
                            <p>Pengolahan Hasil</p>
                        </a>
                    </li>
                    @endif

                    {{-- KELOLA HASIL --}}
                    <li class="nav-header">KELOLA HASIL</li>
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor', 'operator']))
                    <li class="nav-item">
                        <a href="/inventory" class="nav-link {{ request()->is('inventory*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Inventaris & Stok</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/stock-opname" class="nav-link {{ request()->is('stock-opname*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>Stock Opname</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/distributions" class="nav-link {{ request()->is('distributions*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Distribusi</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'keuangan']))
                    <li class="nav-item">
                        <a href="/financial" class="nav-link {{ request()->is('financial*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-wallet"></i>
                            <p>Keuangan</p>
                        </a>
                    </li>
                    @endif

                    {{-- PEMBINAAN --}}
                    <li class="nav-header">PEMBINAAN</li>
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor']))
                    <li class="nav-item">
                        <a href="/training" class="nav-link {{ request()->is('training*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>Pelatihan & Keterampilan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/compliance" class="nav-link {{ request()->is('compliance*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>Sertifikasi</p>
                        </a>
                    </li>
                    @endif

                    {{-- MONITORING & ANALITIK --}}
                    <li class="nav-header">MONITORING</li>
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor']))
                    <li class="nav-item">
                        <a href="/analytics" class="nav-link {{ request()->is('analytics*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Analitik</p>
                        </a>
                    </li>
                    @endif

                    {{-- LAPORAN --}}
                    @if(auth()->user()->hasRole(['super-admin', 'admin', 'supervisor']))
                    <li class="nav-header">LAPORAN</li>
                    <li class="nav-item {{ request()->is('reports*') ? 'menu-open' : '' }}">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->is('reports') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Pusat Laporan<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @php $reportMenu = [
                                'summary' => 'Ringkasan', 'financial' => 'Keuangan', 'wbp' => 'Data WBP',
                                'food-programs' => 'Program Pangan', 'farming' => 'Pertanian', 'harvest' => 'Hasil Panen',
                                'livestock' => 'Peternakan', 'fishery' => 'Perikanan', 'inventory' => 'Inventaris & Stok',
                                'daily-reports' => 'Laporan Harian', 'distributions' => 'Distribusi', 'nutrition' => 'Nutrisi & Menu',
                                'training' => 'Pelatihan', 'compliance' => 'Sertifikasi', 'analytics' => 'Analitik',
                                'processing' => 'Pengolahan', 'stock-opname' => 'Stock Opname', 'land-plots' => 'Manajemen Lahan',
                            ]; @endphp
                            @foreach($reportMenu as $rkey => $rlabel)
                            <li class="nav-item">
                                <a href="{{ route('reports.preview', $rkey) }}" class="nav-link {{ request()->is('reports/' . $rkey . '/preview') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ $rlabel }}</p>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @endif

                    {{-- SISTEM --}}
                    @if(auth()->user()->hasRole(['super-admin']))
                    <li class="nav-header">SISTEM</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.slow-queries.index') }}" class="nav-link {{ request()->is('admin/slow-queries*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Slow Queries</p>
                        </a>
                    </li>
                    @endif

                    {{-- DATA MASTER --}}
                    <li class="nav-header">DATA MASTER</li>
                    @if(auth()->user()->hasRole(['super-admin', 'admin']))
                    <li class="nav-item">
                        <a href="/master/units" class="nav-link {{ request()->is('master/units*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Unit / Lapas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/master/livestock-types" class="nav-link {{ request()->is('master/livestock-types*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-paw"></i>
                            <p>Jenis Ternak</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/master/crop-types" class="nav-link {{ request()->is('master/crop-types*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-seedling"></i>
                            <p>Jenis Tanaman</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/master/inventory-categories" class="nav-link {{ request()->is('master/inventory-categories*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Kategori Inventaris</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/master/financial-categories" class="nav-link {{ request()->is('master/financial-categories*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-wallet"></i>
                            <p>Kategori Keuangan</p>
                        </a>
                    </li>
                    @endif

                    {{-- PENGATURAN --}}
                    <li class="nav-header">PENGATURAN</li>
                    @auth
                    @if(auth()->user()->hasRole('super-admin'))
                    <li class="nav-item">
                        <a href="/admin/users" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/audit-trail" class="nav-link {{ request()->is('admin/audit-trail*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Audit Trail</p>
                        </a>
                    </li>
                    @endif
                    @endauth
                </ul>
            </nav>
        </div>
    </aside>
@endauth

    {{-- Content --}}
    <div class="content-wrapper" style="min-height: calc(100vh - 57px);">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0" style="font-size:22px;font-weight:700;">@yield('title')</h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-right">
                            @yield('header_actions')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer" style="text-align:center;font-size:12px;color:#94a3b8;">
        <strong>&copy; {{ date('Y') }} SIDUMA PANGAN</strong> — Sistem Informasi Terpadu Pemasyarakatan dan Ketahanan Pangan
    </footer>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// CSRF setup for AJAX
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// SweetAlert helper
function confirmDelete(url, name) {
    Swal.fire({
        title: 'Hapus "' + name + '"?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Data berhasil dihapus', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        timer: 4000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        }
    });
}

function showSuccess(msg) {
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: msg, timer: 2000, showConfirmButton: false });
}

function showError(msg) {
    Swal.fire('Error!', msg, 'error');
}

// Form-based delete (for web controllers with redirects)
function confirmDeleteForm(formId) {
    Swal.fire({
        title: 'Hapus Data?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { container: 'swal2-z-index' }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
            document.getElementById(formId).submit();
        }
    });
}

// Generic POST confirmation (for status changes, workflow actions, etc.)
function swalConfirmPost(url, title, text, buttonColor, confirmText) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: buttonColor || '#3085d6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText || 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        customClass: { container: 'swal2-z-index' }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Berhasil diproses',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                    var icon = xhr.status === 422 ? 'warning' : 'error';
                    Swal.fire({
                        icon: icon,
                        title: icon === 'warning' ? 'Perhatian!' : 'Gagal!',
                        text: msg,
                        timer: 4000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        }
    });
}
</script>

{{-- Toast notifications from session flash messages --}}
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 3000,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '{{ session('error') }}',
    timer: 3000,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
</script>
@endif

@if(session('warning'))
<script>
Swal.fire({
    icon: 'warning',
    title: 'Peringatan!',
    text: '{{ session('warning') }}',
    timer: 3000,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
</script>
@endif

@if(session('info'))
<script>
Swal.fire({
    icon: 'info',
    title: 'Info',
    text: '{{ session('info') }}',
    timer: 3000,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
</script>
@endif

@if($errors->any())
<script>
Swal.fire({
    icon: 'error',
    title: 'Validasi Gagal!',
    html: '{!! implode('<br>', $errors->all()) !!}',
    timer: 5000,
    showConfirmButton: true,
    confirmButtonColor: '#ef4444'
});
</script>
@endif

@yield('extra_js')
@stack('scripts')

{{-- Back to top button --}}
<button id="btn-back-top" type="button" aria-label="Kembali ke atas" title="Kembali ke atas"><i class="fas fa-chevron-up"></i></button>

<script>
// === COUNT-UP ANIMATION (site-wide) ===
function parseCountValue(str) {
    var m = String(str).match(/([\d.,]+)/);
    if (!m) return null;
    var s = m[1];
    var lastDot = s.lastIndexOf('.');
    var lastComma = s.lastIndexOf(',');
    var decSep = '';
    if (lastDot > -1 && lastComma > -1) decSep = lastDot > lastComma ? '.' : ',';
    else if (lastDot > -1) decSep = '.';
    else if (lastComma > -1) decSep = ',';
    var dec = 0;
    var idx = decSep === '.' ? lastDot : lastComma;
    if (decSep !== '' && idx > -1 && idx < s.length - 1) {
        var after = s.substring(idx + 1);
        if (/^\d+$/.test(after) && after.length <= 2 && s.indexOf(decSep) === idx) dec = after.length;
    }
    var cleaned = s.replace(/[.,]/g, '');
    if (dec > 0) cleaned = cleaned.slice(0, -dec) + '.' + cleaned.slice(-dec);
    var num = parseFloat(cleaned);
    return isNaN(num) ? null : num;
}
function formatCountNum(n, dec, sep) {
    var parts = n.toFixed(dec).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep);
    return dec > 0 ? parts.join(',') : parts[0];
}
function countUpElement(el, opts) {
    if (!el || !el.textContent) return;
    opts = opts || {};
    var text = el.textContent;
    var m = String(text).match(/^([^\d]*)([\d.,]+)(.*)$/);
    if (!m) return;
    var prefix = m[1], numStr = m[2], suffix = m[3];
    var sep = numStr.indexOf(',') > -1 ? ',' : '.';
    var dec = 0;
    var dm = numStr.match(/([.,])(\d{1,2})$/);
    if (dm) dec = dm[2].length;
    var to = (typeof opts.to === 'number') ? opts.to : parseCountValue(numStr);
    if (to === null) return;
    var from;
    if (typeof opts.from === 'number') from = opts.from;
    else {
        from = parseCountValue(text);
        if (from === null || from === to) from = 0;
    }
    var duration = opts.duration || 800;
    var token = (parseInt(el.dataset.countToken || '0', 10) || 0) + 1;
    el.dataset.countToken = token;
    var start = null;
    function frame(ts) {
        if (parseInt(el.dataset.countToken, 10) !== token) return;
        if (!start) start = ts;
        var p = Math.min((ts - start) / duration, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = prefix + formatCountNum(from + (to - from) * eased, dec, sep) + suffix;
        if (p < 1) requestAnimationFrame(frame);
        else el.textContent = prefix + formatCountNum(to, dec, sep) + suffix;
    }
    requestAnimationFrame(frame);
}
function countUpIn(el) {
    countUpElement(el, { from: 0 });
}

// === SCROLL EFFECTS (site-wide) ===
document.documentElement.classList.add('dash-js');
(function() {
    var navEl = document.querySelector('.main-header');
    var filterRow = document.querySelector('.filter-header');
    if (filterRow) filterRow.style.top = (navEl ? navEl.offsetHeight : 57) + 'px';

    var dashRows = document.querySelectorAll('.dash-row');
    var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if ('IntersectionObserver' in window && !reducedMotion) {
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    e.target.querySelectorAll('.stat-value').forEach(countUpIn);
                    obs.unobserve(e.target);
                }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -24px 0px' });
        dashRows.forEach(function(r) {
            // Baris yang sudah terlihat langsung tampil (hindari flash), sisanya diamati saat discroll
            var rect = r.getBoundingClientRect();
            if (rect.top < window.innerHeight) {
                r.classList.add('visible');
                r.querySelectorAll('.stat-value').forEach(countUpIn);
            } else {
                obs.observe(r);
            }
        });
    } else {
        dashRows.forEach(function(r) { r.classList.add('visible'); });
    }

    // Count-up untuk stat value di luar dash-row (mis. halaman show)
    if (!reducedMotion) {
        document.querySelectorAll('.stat-value').forEach(function(el) {
            if (el.closest('.dash-row')) return;
            var rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight) countUpIn(el);
        });
    }

    var btnTop = document.getElementById('btn-back-top');
    if (btnTop) {
        window.addEventListener('scroll', function() {
            btnTop.classList.toggle('show', window.scrollY > 420);
        }, { passive: true });
        btnTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();

// === SCROLLBAR HINT TABEL (bayangan kanan saat masih bisa discroll) ===
function initTableScrollHints() {
    document.querySelectorAll('.table-responsive').forEach(function(w) {
        function check() {
            var scrollable = w.scrollWidth > w.clientWidth + 8;
            var atEnd = w.scrollLeft + w.clientWidth >= w.scrollWidth - 8;
            w.classList.toggle('has-scroll', scrollable && !atEnd);
        }
        check();
        w.addEventListener('scroll', check, { passive: true });
        window.addEventListener('resize', check, { passive: true });
    });
}
initTableScrollHints();
</script>
</body>
</html>

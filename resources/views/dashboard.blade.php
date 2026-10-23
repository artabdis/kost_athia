<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Kost Athia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            color-scheme: light;
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
            --blue-light: #eff6ff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --surface: #ffffff;
            --background: #f8fafc;
            --green: #15803d;
            --green-light: #f0fdf4;
            --amber: #b45309;
            --amber-light: #fffbeb;
            --red: #b91c1c;
            --red-light: #fef2f2;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--background);
            color: var(--ink);
            font-family: 'Inter', Arial, sans-serif;
            font-size: 14px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 3px solid #93c5fd;
            outline-offset: 2px;
        }

        .app-shell {
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 5;
            display: flex;
            width: 252px;
            flex-direction: column;
            border-right: 1px solid var(--line);
            background: var(--surface);
            padding: 26px 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin: 0 8px 36px;
            color: var(--blue);
            font-size: 19px;
            font-weight: 800;
        }

        .brand-mark {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            border-radius: 11px;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 18px;
        }

        .nav-label {
            margin: 0 10px 12px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .nav-links {
            display: grid;
            gap: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            min-height: 43px;
            gap: 12px;
            border-radius: 9px;
            padding: 0 12px;
            color: #475569;
            font-weight: 600;
            transition: background .18s ease, color .18s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--blue-light);
            color: var(--blue);
        }

        .mobile-logout {
            display: none;
        }

        .mobile-logout button {
            border: 0;
            background: transparent;
            cursor: pointer;
            font: inherit;
            text-align: left;
        }

        .nav-icon {
            display: inline-grid;
            width: 20px;
            place-items: center;
            font-size: 16px;
        }

        .sidebar-bottom {
            margin-top: auto;
        }

        .account-mini {
            overflow: hidden;
            margin: 0 4px 16px;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 12px;
        }

        .account-mini strong,
        .account-mini span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-mini strong {
            font-size: 13px;
        }

        .account-mini span {
            margin-top: 5px;
            color: var(--muted);
            font-size: 11px;
        }

        .logout-form {
            margin: 0 4px;
        }

        .logout-button {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 9px;
            background: white;
            color: #475569;
            cursor: pointer;
            font-weight: 600;
            padding: 11px 12px;
            text-align: left;
        }

        .logout-button:hover {
            border-color: #fecaca;
            background: var(--red-light);
            color: var(--red);
        }

        .main-content {
            min-width: 0;
            margin-left: 252px;
            padding: 34px 42px 52px;
        }

        .content-wrap {
            width: min(1240px, 100%);
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--blue);
            font-size: 12px;
            font-weight: 700;
        }

        h1 {
            margin: 0;
            font-size: clamp(24px, 3vw, 31px);
            letter-spacing: -.035em;
        }

        .page-subtitle {
            margin: 9px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .role-pill {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            background: var(--blue-light);
            color: var(--blue-dark);
            font-size: 12px;
            font-weight: 700;
            padding: 8px 12px;
        }

        .alert {
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 13px 16px;
            line-height: 1.55;
        }

        .alert.success {
            border-color: #bbf7d0;
            background: var(--green-light);
            color: #166534;
        }

        .alert.error {
            border-color: #fecaca;
            background: var(--red-light);
            color: #991b1b;
        }

        .alert.info {
            border-color: #bfdbfe;
            background: var(--blue-light);
            color: #1e40af;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card,
        .panel,
        .info-card {
            border: 1px solid var(--line);
            border-radius: 13px;
            background: var(--surface);
            box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
        }

        .stat-card {
            padding: 19px;
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .stat-icon {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            border-radius: 10px;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 17px;
        }

        .stat-value {
            margin-top: 15px;
            font-size: 29px;
            font-weight: 800;
            letter-spacing: -.04em;
        }

        .stat-description {
            margin-top: 5px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.5;
        }

        .section {
            scroll-margin-top: 24px;
            margin-top: 26px;
        }

        .section-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 13px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 17px;
            letter-spacing: -.02em;
        }

        .section-heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .panel {
            overflow: hidden;
        }

        .panel-body {
            padding: 20px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            border-bottom: 1px solid var(--line);
            background: #fbfdff;
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .06em;
            padding: 13px 16px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        td {
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 12px;
            padding: 14px 16px;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        td strong {
            color: var(--ink);
            font-size: 12px;
        }

        .muted {
            color: var(--muted);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            padding: 6px 9px;
            white-space: nowrap;
        }

        .badge.waiting {
            background: var(--amber-light);
            color: var(--amber);
        }

        .badge.approved,
        .badge.available {
            background: var(--green-light);
            color: var(--green);
        }

        .badge.rejected,
        .badge.occupied {
            background: var(--red-light);
            color: var(--red);
        }

        .badge.neutral {
            background: #f1f5f9;
            color: #475569;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 37px;
            border: 1px solid transparent;
            border-radius: 8px;
            background: var(--blue);
            color: white;
            cursor: pointer;
            font-size: 11px;
            font-weight: 700;
            padding: 9px 12px;
            transition: background .18s ease, border-color .18s ease;
        }

        .button:hover {
            background: var(--blue-dark);
        }

        .button.secondary {
            border-color: var(--line);
            background: white;
            color: #334155;
        }

        .button.secondary:hover {
            border-color: #bfdbfe;
            background: var(--blue-light);
            color: var(--blue);
        }

        .button.danger {
            background: #dc2626;
        }

        .button.danger:hover {
            background: #b91c1c;
        }

        .button.small {
            min-height: 31px;
            padding: 7px 10px;
        }

        .payment-actions {
            min-width: 210px;
        }

        .payment-actions form {
            display: grid;
            gap: 7px;
            margin-top: 9px;
        }

        .note-input {
            width: 100%;
            min-width: 190px;
            border: 1px solid var(--line);
            border-radius: 7px;
            padding: 8px 9px;
        }

        .proof-link {
            display: inline-block;
        }

        .proof-image {
            display: block;
            width: 54px;
            height: 44px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--background);
            object-fit: cover;
        }

        .empty-state {
            padding: 30px 20px;
            color: var(--muted);
            text-align: center;
            line-height: 1.65;
        }

        .empty-state strong {
            display: block;
            margin-bottom: 5px;
            color: var(--ink);
        }

        .empty-state .button {
            margin-top: 13px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .info-card {
            padding: 18px;
        }

        .info-card h3 {
            margin: 0 0 12px;
            font-size: 14px;
        }

        .detail-list {
            display: grid;
            gap: 10px;
            margin: 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            line-height: 1.5;
        }

        .detail-row dt {
            color: var(--muted);
        }

        .detail-row dd {
            margin: 0;
            color: var(--ink);
            font-weight: 600;
            text-align: right;
        }

        .notice {
            border: 1px solid #bfdbfe;
            border-radius: 9px;
            background: var(--blue-light);
            color: #1e40af;
            font-size: 11px;
            line-height: 1.6;
            margin-top: 13px;
            padding: 11px 13px;
        }

        .notice.warning {
            border-color: #fde68a;
            background: var(--amber-light);
            color: #92400e;
        }

        .profile-card {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .avatar {
            display: grid;
            width: 46px;
            height: 46px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 50%;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 16px;
            font-weight: 800;
        }

        .profile-card strong {
            display: block;
        }

        .profile-card span {
            display: block;
            margin-top: 5px;
            color: var(--muted);
        }

        @media (max-width: 1100px) {
            .main-content {
                padding-right: 26px;
                padding-left: 26px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .sidebar {
                position: sticky;
                inset: 0 0 auto;
                width: 100%;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid var(--line);
                padding: 12px 16px 10px;
            }

            .brand {
                margin: 0 0 10px;
                font-size: 17px;
            }

            .brand-mark {
                width: 32px;
                height: 32px;
            }

            .nav-label,
            .sidebar-bottom {
                display: none;
            }

            .nav-links {
                display: flex;
                overflow-x: auto;
                gap: 5px;
                padding-bottom: 2px;
                scrollbar-width: thin;
            }

            .mobile-logout {
                display: block;
                flex: 0 0 auto;
                margin: 0;
            }

            .nav-link {
                min-height: 37px;
                flex: 0 0 auto;
                gap: 7px;
                padding: 0 10px;
                font-size: 11px;
            }

            .nav-icon {
                width: auto;
                font-size: 13px;
            }

            .main-content {
                margin-left: 0;
                padding: 26px 18px 40px;
            }

            .page-header {
                margin-bottom: 21px;
            }

            .section {
                scroll-margin-top: 120px;
            }
        }

        @media (max-width: 520px) {
            .main-content {
                padding-right: 14px;
                padding-left: 14px;
            }

            .page-header {
                flex-direction: column;
                gap: 12px;
            }

            .stats-grid {
                gap: 10px;
            }

            .stat-card {
                padding: 14px;
            }

            .stat-value {
                font-size: 25px;
            }

            .stat-label {
                font-size: 11px;
            }

            .stat-icon {
                width: 31px;
                height: 31px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .panel-body {
                padding: 15px;
            }

            .section-heading {
                align-items: flex-start;
            }

            .detail-row {
                gap: 9px;
            }
        }
    </style>
</head>

<body>

    @php
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        $statusLabels = [
            'pending' => 'Belum mengunggah',
            'submitted' => 'Menunggu verifikasi',
            'approved' => 'Lunas / disetujui',
            'rejected' => 'Ditolak',
        ];

        $statusClasses = [
            'pending' => 'neutral',
            'submitted' => 'waiting',
            'approved' => 'approved',
            'rejected' => 'rejected',
        ];

        $roomStatusLabels = [
            'tersedia' => 'Tersedia',
            'terisi' => 'Terisi',
            'belum_dikonfirmasi' => 'Belum dikonfirmasi',
        ];
    @endphp

    <div class="app-shell">

        <aside class="sidebar" aria-label="Navigasi dashboard">

            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark" aria-hidden="true">A</span>
                <span>Kost Athia</span>
            </a>

            <p class="nav-label">
                Menu {{ $isAdmin ? 'Admin' : 'Penghuni' }}
            </p>

            <nav class="nav-links">

                @if ($isAdmin)

                    {{-- ==================== ADMIN ==================== --}}
                    {{-- BAGIAN ADMIN TIDAK DIUBAH --}}

                    <a class="nav-link active" href="#overview">
                        <span class="nav-icon" aria-hidden="true">⌂</span>
                        Dashboard
                    </a>

                    <a class="nav-link" href="#rooms">
                        <span class="nav-icon" aria-hidden="true">▦</span>
                        Data Kamar
                    </a>

                    <a class="nav-link" href="#residents">
                        <span class="nav-icon" aria-hidden="true">♙</span>
                        Data Penghuni
                    </a>

                    <a class="nav-link" href="#payments">
                        <span class="nav-icon" aria-hidden="true">Rp</span>
                        Pembayaran
                    </a>

                    <a class="nav-link" href="{{ route('admin.keluhan.index') }}">
                        <span class="nav-icon" aria-hidden="true">⚠</span>
                        Keluhan
                    </a>



                @else

                    {{-- ==================== PENGHUNI ==================== --}}

                    <a class="nav-link active" href="#overview">
                        <span class="nav-icon" aria-hidden="true">⌂</span>
                        Dashboard
                    </a>


                    <a class="nav-link" href="#my-payment">
                        <span class="nav-icon" aria-hidden="true">Rp</span>
                        Pembayaran
                    </a>

                    <a class="nav-link" href="#history">
                        <span class="nav-icon" aria-hidden="true">◷</span>
                        Riwayat Pembayaran
                    </a>

                    <a class="nav-link" href="{{ route('keluhan.index') }}">
                        <span class="nav-icon" aria-hidden="true">⚠</span>
                        Keluhan
                    </a>

                    <a class="nav-link" href="#profile">
                        <span class="nav-icon" aria-hidden="true">♙</span>
                        Profil Saya
                    </a>

                @endif

                <form class="mobile-logout" action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button class="nav-link" type="submit">
                        <span class="nav-icon" aria-hidden="true">↪</span>
                        Keluar
                    </button>
                </form>

            </nav>

            <div class="sidebar-bottom">

                <div class="account-mini">
                    <strong>{{ $currentUser->name }}</strong>
                    <span>{{ $currentUser->email }}</span>
                </div>

                <form class="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button class="logout-button" type="submit">
                        Keluar dari akun
                    </button>
                </form>

            </div>

        </aside>

        <main class="main-content" id="overview">

            <div class="content-wrap">

                <header class="page-header">

                    <div>
                        <p class="eyebrow">
                            KOST ATHIA / DASHBOARD
                        </p>

                        <h1>
                            Selamat datang, {{ $currentUser->name }}
                        </h1>

                        <p class="page-subtitle">
                            {{ $isAdmin
                                ? 'Pantau kondisi kamar, penghuni, booking, dan pembayaran.'
                                : 'Kelola informasi kamar dan pantau tagihan pembayaran Anda.' }}
                        </p>
                    </div>

                    <span class="role-pill">
                        {{ $isAdmin ? 'Administrator' : 'Penghuni' }}
                    </span>

                </header>

                @if (session('success'))
                    <div class="alert success" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert info" role="status">
                        {{ session('info') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert error" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif


                {{-- ========================================================= --}}
                {{-- ======================= ADMIN =========================== --}}
                {{-- ========================================================= --}}

                @if ($isAdmin)

                    {{-- STATS ADMIN --}}

                    <section class="stats-grid" aria-label="Ringkasan dashboard">

                        <article class="stat-card">

                            <div class="stat-top">
                                <span class="stat-label">
                                    Total Kamar
                                </span>

                                <span class="stat-icon" aria-hidden="true">
                                    ▦
                                </span>
                            </div>

                            <div class="stat-value">
                                {{ number_format($stats['rooms']) }}
                            </div>

                            <p class="stat-description">
                                Kamar tercatat di database.
                            </p>

                        </article>


                        <article class="stat-card">

                            <div class="stat-top">
                                <span class="stat-label">
                                    Kamar Tersedia
                                </span>

                                <span class="stat-icon" aria-hidden="true">
                                    ✓
                                </span>
                            </div>

                            <div class="stat-value">
                                {{ number_format($stats['availableRooms']) }}
                            </div>

                            <p class="stat-description">
                                Berdasarkan status kamar tersedia.
                            </p>

                        </article>


                        <article class="stat-card">

                            <div class="stat-top">
                                <span class="stat-label">
                                    Penghuni Terverifikasi
                                </span>

                                <span class="stat-icon" aria-hidden="true">
                                    ♙
                                </span>
                            </div>

                            <div class="stat-value">
                                {{ number_format($stats['residents']) }}
                            </div>

                            <p class="stat-description">
                                Akun penghuni dengan pembayaran disetujui.
                            </p>

                        </article>


                        <article class="stat-card">

                            <div class="stat-top">
                                <span class="stat-label">
                                    Menunggu Verifikasi
                                </span>

                                <span class="stat-icon" aria-hidden="true">
                                    ◷
                                </span>
                            </div>

                            <div class="stat-value">
                                {{ number_format($stats['pendingPayments']) }}
                            </div>

                            <p class="stat-description">
                                Bukti pembayaran yang menunggu pemeriksaan.
                            </p>

                        </article>

                    </section>


                    {{-- ==================== PEMBAYARAN ADMIN ==================== --}}

                    <section class="section" id="payments">

                        <div class="section-heading">

                            <div>

                                <h2>Pembayaran</h2>

                                <p>
                                    Pantau pembayaran awal dan tagihan sewa penghuni Kost Athia.
                                </p>

                            </div>

                        </div>


                        {{-- PEMBAYARAN AWAL --}}

                        <div class="panel">

                            <div class="panel-body" style="border-bottom: 1px solid var(--line);">

                                <div style="display:flex; align-items:center; justify-content:space-between; gap:15px; flex-wrap:wrap;">

                                    <div>

                                        <h3 style="margin:0; font-size:14px;">
                                            Verifikasi Pembayaran Awal
                                        </h3>

                                        <p style="margin:5px 0 0; color:var(--muted); font-size:11px;">
                                            Bukti pembayaran awal yang dikirim penghuni setelah registrasi.
                                        </p>

                                    </div>

                                    <span class="badge neutral">
                                        {{ $payments->count() }} data
                                    </span>

                                </div>

                            </div>


                            @if ($payments->isEmpty())

                                <div class="empty-state">

                                    <strong>
                                        Belum ada bukti pembayaran.
                                    </strong>

                                    Pembayaran awal penghuni yang sudah mengunggah bukti akan muncul di sini.

                                </div>

                            @else

                                <div class="table-wrap">

                                    <table>

                                        <thead>

                                            <tr>
                                                <th>Penghuni</th>
                                                <th>Pembaruan terakhir</th>
                                                <th>Status</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            @foreach ($payments as $payment)

                                                <tr>

                                                    <td>

                                                        <strong>
                                                            {{ $payment->name }}
                                                        </strong>

                                                        <br>

                                                        <span class="muted">
                                                            {{ $payment->email }}
                                                        </span>

                                                    </td>

                                                    <td>
                                                        {{ $payment->updated_at?->format('d M Y, H:i') ?? '—' }}
                                                    </td>

                                                    <td>

                                                        <span class="badge {{ $statusClasses[$payment->payment_status] ?? 'neutral' }}">

                                                            {{ $statusLabels[$payment->payment_status] ?? ucfirst($payment->payment_status) }}

                                                        </span>

                                                    </td>

                                                    <td>

                                                        <a class="proof-link"
                                                            href="{{ route('admin.payment.proof', $payment) }}"
                                                            target="_blank"
                                                            rel="noopener"
                                                            aria-label="Buka bukti pembayaran {{ $payment->name }}">

                                                            <img class="proof-image"
                                                                src="{{ route('admin.payment.proof', $payment) }}"
                                                                alt="Bukti pembayaran {{ $payment->name }}">

                                                        </a>

                                                    </td>

                                                    <td class="payment-actions">

                                                        @if ($payment->payment_status === 'submitted')

                                                            <form
                                                                action="{{ route('admin.payment.verify', $payment) }}"
                                                                method="POST"
                                                                onsubmit="return confirmVerification(event, this)">

                                                                @csrf

                                                                <input class="note-input"
                                                                    type="text"
                                                                    name="note"
                                                                    maxlength="1000"
                                                                    placeholder="Alasan penolakan (wajib jika menolak)"
                                                                    aria-label="Alasan penolakan untuk {{ $payment->name }}">

                                                                <div style="display:flex; gap:7px;">

                                                                    <button class="button small"
                                                                        type="submit"
                                                                        name="status"
                                                                        value="approved">

                                                                        Verifikasi

                                                                    </button>

                                                                    <button class="button danger small"
                                                                        type="submit"
                                                                        name="status"
                                                                        value="rejected">

                                                                        Tolak

                                                                    </button>

                                                                </div>

                                                            </form>

                                                        @else

                                                            <span class="muted">
                                                                Sudah diproses
                                                            </span>

                                                        @endif

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @endif

                        </div>


                        {{-- ================= TAGIHAN PENGHUNI ================= --}}

                        <div class="panel" style="margin-top:16px;">

                            <div class="panel-body" style="border-bottom:1px solid var(--line);">

                                <div style="display:flex; align-items:center; justify-content:space-between; gap:15px; flex-wrap:wrap;">

                                    <div>

                                        <h3 style="margin:0; font-size:14px;">
                                            Tagihan Penghuni
                                        </h3>

                                        <p style="margin:5px 0 0; color:var(--muted); font-size:11px;">
                                            Daftar tagihan sewa yang dibuat untuk penghuni.
                                        </p>

                                    </div>

                                    <span class="badge neutral">
                                        {{ $tagihans->count() }} tagihan
                                    </span>

                                </div>

                            </div>


                            @if ($tagihans->isEmpty())

                                <div class="empty-state">

                                    <strong>
                                        Belum ada tagihan.
                                    </strong>

                                    Tagihan penghuni yang sudah dibuat akan muncul di sini.

                                </div>

                            @else

                                <div class="table-wrap">

                                    <table>

                                        <thead>

                                            <tr>
                                                <th>Penghuni</th>
                                                <th>Kamar</th>
                                                <th>Periode</th>
                                                <th>Nominal</th>
                                                <th>Jatuh Tempo</th>
                                                <th>Status</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>

                                        </thead>


                                        <tbody>

                                            @foreach ($tagihans as $tagihan)

                                                <tr>

                                                    {{-- PENGHUNI --}}
                                                    <td>

                                                        <strong>
                                                            {{ $tagihan->user?->name ?? 'Pengguna tidak tersedia' }}
                                                        </strong>

                                                        <br>

                                                        <span class="muted">
                                                            {{ $tagihan->user?->email ?? '—' }}
                                                        </span>

                                                    </td>


                                                    {{-- KAMAR --}}
                                                    <td>

                                                        <strong>
                                                            {{ $tagihan->room?->kode_kamar ?? '—' }}
                                                        </strong>

                                                        @if ($tagihan->room?->nama)

                                                            <br>

                                                            <span class="muted">
                                                                {{ $tagihan->room->nama }}
                                                            </span>

                                                        @endif

                                                    </td>


                                                    {{-- PERIODE --}}
                                                    <td>
                                                        {{ $tagihan->periode }}
                                                    </td>


                                                    {{-- NOMINAL --}}
                                                    <td>

                                                        <strong>
                                                            Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                                        </strong>

                                                    </td>


                                                    {{-- JATUH TEMPO --}}
                                                    <td>
                                                        {{ $tagihan->jatuh_tempo?->format('d M Y') ?? '—' }}
                                                    </td>


                                                    {{-- STATUS --}}
                                                    <td>

                                                        @if ($tagihan->status === 'lunas')

                                                            <span class="badge approved">
                                                                Lunas
                                                            </span>

                                                        @elseif ($tagihan->status === 'menunggu_verifikasi')

                                                            <span class="badge waiting">
                                                                Menunggu verifikasi
                                                            </span>

                                                        @elseif ($tagihan->status === 'ditolak')

                                                            <span class="badge rejected">
                                                                Ditolak
                                                            </span>

                                                        @else

                                                            <span class="badge neutral">
                                                                Belum bayar
                                                            </span>

                                                        @endif

                                                    </td>


                                                    {{-- BUKTI --}}
                                                    <td>

                                                        @if ($tagihan->bukti_pembayaran)

                                                            <a
                                                                class="button secondary small"
                                                                href="{{ route('admin.tagihan.proof', $tagihan) }}"
                                                                target="_blank"
                                                                rel="noopener">

                                                                Lihat Bukti

                                                            </a>

                                                        @else

                                                            <span class="muted">
                                                                Belum ada
                                                            </span>

                                                        @endif

                                                    </td>


                                                    {{-- AKSI --}}
                                                    <td>

                                                        @if ($tagihan->status === 'menunggu_verifikasi')

                                                            <form
                                                                action="{{ route('admin.tagihan.verify', $tagihan) }}"
                                                                method="POST"
                                                                style="display:grid; gap:7px; min-width:190px;">

                                                                @csrf

                                                                <input
                                                                    class="note-input"
                                                                    type="text"
                                                                    name="catatan"
                                                                    maxlength="1000"
                                                                    placeholder="Alasan jika ditolak">

                                                                <div style="display:flex; gap:7px;">

                                                                    <button
                                                                        class="button small"
                                                                        type="submit"
                                                                        name="status"
                                                                        value="lunas"
                                                                        onclick="return confirm('Verifikasi pembayaran tagihan ini?')">

                                                                        Verifikasi

                                                                    </button>


                                                                    <button
                                                                        class="button danger small"
                                                                        type="submit"
                                                                        name="status"
                                                                        value="ditolak"
                                                                        onclick="return confirm('Tolak pembayaran tagihan ini?')">

                                                                        Tolak

                                                                    </button>

                                                                </div>

                                                            </form>

                                                        @elseif ($tagihan->status === 'lunas')

                                                            <span class="muted">
                                                                Sudah lunas
                                                            </span>

                                                        @elseif ($tagihan->status === 'ditolak')

                                                            <span class="muted">
                                                                Ditolak
                                                            </span>

                                                        @else

                                                            <span class="muted">
                                                                Belum ada pembayaran
                                                            </span>

                                                        @endif

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @endif

                        </div>


                        <p class="notice">

                            Pembayaran awal dan tagihan sewa merupakan dua data yang berbeda.
                            Pembayaran awal tetap menggunakan sistem verifikasi bukti pembayaran,
                            sedangkan tagihan digunakan untuk mencatat pembayaran sewa penghuni.

                        </p>

                    </section>


                    {{-- ==================== DATA KAMAR ADMIN ==================== --}}

                    <section class="section" id="rooms">

                        <div class="section-heading">

                            <div>

                                <h2>Data kamar</h2>

                                <p>
                                    Informasi kamar yang tersimpan di database Kost Athia.
                                </p>

                            </div>

                            <a class="button secondary"
                                href="{{ route('admin.rooms.index') }}">
                                Kelola Data Kamar
                            </a>

                        </div>

                        <div class="panel">

                            @if ($rooms->isEmpty())

                                <div class="empty-state">

                                    <strong>
                                        Belum ada data kamar.
                                    </strong>

                                    Kamar akan tampil setelah tersedia di database.

                                </div>

                            @else

                                <div class="table-wrap">

                                    <table>

                                        <thead>

                                            <tr>
                                                <th>Kode</th>
                                                <th>Nama Kamar</th>
                                                <th>Lokasi</th>
                                                <th>Harga</th>
                                                <th>Status</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            @foreach ($rooms as $room)

                                                <tr>

                                                    <td>
                                                        <strong>
                                                            {{ $room->kode_kamar }}
                                                        </strong>
                                                    </td>

                                                    <td>
                                                        {{ $room->nama }}
                                                    </td>

                                                    <td>
                                                        {{ $room->lokasi }}
                                                    </td>

                                                    <td>
                                                        Rp {{ number_format($room->harga, 0, ',', '.') }}
                                                    </td>

                                                    <td>

                                                        <span class="badge
                                                            {{ $room->status === 'tersedia'
                                                                ? 'available'
                                                                : ($room->status === 'terisi'
                                                                    ? 'occupied'
                                                                    : 'neutral') }}">

                                                            {{ $roomStatusLabels[$room->status] ?? ucfirst($room->status) }}

                                                        </span>

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @endif

                        </div>

                    </section>


                    {{-- ==================== DATA PENGHUNI ADMIN ==================== --}}

                    <section class="section" id="residents">

                        <div class="section-heading">

                            <div>

                                <h2>Data penghuni</h2>

                                <p>
                                    Akun dengan role user; status pembayaran ditampilkan sebagai status verifikasi yang tersedia.
                                </p>

                            </div>

                            <a class="button secondary"
                                href="{{ route('admin.residents.index') }}">
                                Kelola Data Penghuni
                            </a>

                        </div>

                        <div class="panel">

                            @if ($residents->isEmpty())

                                <div class="empty-state">

                                    <strong>
                                        Belum ada akun penghuni.
                                    </strong>

                                    Akun pengguna akan muncul di sini.

                                </div>

                            @else

                                <div class="table-wrap">

                                    <table>

                                        <thead>

                                            <tr>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Status pembayaran</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            @foreach ($residents as $resident)

                                                <tr>

                                                    <td>
                                                        <strong>
                                                            {{ $resident->name }}
                                                        </strong>
                                                    </td>

                                                    <td>
                                                        {{ $resident->email }}
                                                    </td>

                                                    <td>

                                                        <span class="badge {{ $statusClasses[$resident->payment_status] ?? 'neutral' }}">

                                                            {{ $statusLabels[$resident->payment_status] ?? ucfirst($resident->payment_status) }}

                                                        </span>

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @endif

                        </div>

                    </section>


    


                @else

                    {{-- ========================================================= --}}
                    {{-- ====================== PENGHUNI ======================== --}}
                    {{-- ========================================================= --}}

                    @php
                        $paymentStatus = $currentUser->payment_status;

                        $paymentStatusLabel =
                            $statusLabels[$paymentStatus] ?? ucfirst($paymentStatus);

                        $paymentStatusClass =
                            $statusClasses[$paymentStatus] ?? 'neutral';
                    @endphp


                    


                    {{-- ==================== PEMBAYARAN PENGHUNI ==================== --}}

                    <section class="section" id="my-payment">

                        <div class="section-heading">

                            <div>

                                <h2>Pembayaran</h2>

                                <p>
                                    Tagihan sewa kamar Anda.
                                </p>

                            </div>

                        </div>

                        <div class="panel">

                            @if ($tagihans->isEmpty())

                                <div class="empty-state">

                                    <strong>
                                        Belum ada tagihan.
                                    </strong>

                                    Saat ini belum ada tagihan yang harus dibayar.

                                </div>

                            @else

                                @php
                                    $tagihanAktif = $tagihans->first();
                                @endphp

                                <div class="panel-body">

                                    <article class="info-card">

                                        <h3>
                                            Tagihan Anda
                                        </h3>

                                        <dl class="detail-list">

                                            <div class="detail-row">
                                                <dt>Jenis pembayaran</dt>

                                                <dd>
                                                    {{ $tagihanAktif->jenis_pembayaran }}
                                                </dd>
                                            </div>

                                            <div class="detail-row">
                                                <dt>Kamar</dt>

                                                <dd>

                                                    {{ $tagihanAktif->room?->kode_kamar ?? '—' }}

                                                    @if ($tagihanAktif->room?->nama)
                                                        - {{ $tagihanAktif->room->nama }}
                                                    @endif

                                                </dd>

                                            </div>

                                            <div class="detail-row">
                                                <dt>Periode</dt>

                                                <dd>
                                                    {{ $tagihanAktif->periode }}
                                                </dd>
                                            </div>

                                            <div class="detail-row">
                                                <dt>Nominal</dt>

                                                <dd>
                                                    Rp {{ number_format($tagihanAktif->nominal, 0, ',', '.') }}
                                                </dd>
                                            </div>

                                            <div class="detail-row">
                                                <dt>Jatuh tempo</dt>

                                                <dd>
                                                    {{ $tagihanAktif->jatuh_tempo?->format('d M Y') ?? '—' }}
                                                </dd>

                                            </div>

                                            <div class="detail-row">
                                                <dt>Status</dt>

                                                <dd>

                                                    @if ($tagihanAktif->status === 'lunas')

                                                        <span class="badge approved">
                                                            Lunas
                                                        </span>

                                                    @elseif ($tagihanAktif->status === 'menunggu_verifikasi')

                                                        <span class="badge waiting">
                                                            Menunggu verifikasi
                                                        </span>

                                                    @elseif ($tagihanAktif->status === 'ditolak')

                                                        <span class="badge rejected">
                                                            Ditolak
                                                        </span>

                                                    @else

                                                        <span class="badge neutral">
                                                            Belum bayar
                                                        </span>

                                                    @endif

                                                </dd>

                                            </div>

                                        </dl>


                                        @if ($tagihanAktif->status === 'ditolak' && $tagihanAktif->catatan)

                                            <div class="notice warning">

                                                <strong>
                                                    Catatan admin:
                                                </strong>

                                                {{ $tagihanAktif->catatan }}

                                            </div>

                                        @elseif ($tagihanAktif->status === 'menunggu_verifikasi')

                                            <div class="notice">

                                                Bukti pembayaran sudah dikirim dan sedang menunggu verifikasi admin.

                                            </div>

                                        @elseif ($tagihanAktif->status === 'lunas')

                                            <div class="notice">

                                                Tagihan ini sudah lunas.

                                            </div>

                                        @endif


                                        @if (in_array($tagihanAktif->status, ['belum_bayar', 'ditolak'], true))

                                            <a href="{{ route('tagihan.show', $tagihanAktif->id) }}"
                                                class="button">

                                                Bayar Sekarang

                                            </a>

                                        @endif

                                    </article>

                                </div>

                            @endif

                        </div>

                    </section>


                    {{-- ==================== KAMAR YANG DIBOOKING ==================== --}}
                    <section class="section" id="my-room">
                        <div class="section-heading">
                            <div>
                                <h2>Kamar yang Dibooking</h2>
                                <p>Informasi kamar yang Anda pilih saat melakukan booking.</p>
                            </div>
                        </div>

                        <div class="panel">
                            @if ($bookings->isEmpty())
                                <div class="empty-state">
                                    <strong>Belum ada kamar yang dibooking.</strong>
                                    <p>Saat ini belum ada data booking kamar pada akun Anda.</p>
                                </div>
                            @else
                                <div class="panel-body">
                                    @foreach ($bookings as $booking)
                                        <div class="info-card" style="margin-bottom: 15px;">
                                            <div class="detail-list">

                                                <div class="detail-row">
                                                    <span>Kode Kamar</span>
                                                    <strong>{{ $booking->room?->kode_kamar ?? '—' }}</strong>
                                                </div>

                                                <div class="detail-row">
                                                    <span>Nama Kamar</span>
                                                    <strong>{{ $booking->room?->nama ?? '—' }}</strong>
                                                </div>

                                                <div class="detail-row">
                                                    <span>Harga Kamar</span>
                                                    <strong>
                                                        @if ($booking->room?->harga !== null)
                                                            Rp {{ number_format($booking->room->harga, 0, ',', '.') }}
                                                        @else
                                                            —
                                                        @endif
                                                    </strong>
                                                </div>

                                                <div class="detail-row">
                                                    <span>Status Pembayaran</span>
                                                    <strong>
                                                        @if (Auth::user()->payment_status === 'approved')
                                                            Lunas
                                                        @else
                                                            Belum Lunas
                                                        @endif
                                                    </strong>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- ==================== RIWAYAT PEMBAYARAN ==================== --}}

                    <section class="section" id="history">

                        <div class="section-heading">

                            <div>

                                <h2>Riwayat Pembayaran</h2>

                                <p>
                                    Informasi pembayaran yang tersimpan pada akun Anda.
                                </p>

                            </div>

                        </div>

                        <div class="panel">

                            @if ($currentUser->payment_proof)

                                <div class="table-wrap">

                                    <table>

                                        <thead>

                                            <tr>
                                                <th>No.</th>
                                                <th>Jenis / periode</th>
                                                <th>Tanggal pembayaran</th>
                                                <th>Nominal</th>
                                                <th>Status</th>
                                                <th>Pembaruan data</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <tr>

                                                <td>
                                                    1
                                                </td>

                                                <td>

                                                    Tidak dicatat

                                                    <br>

                                                    <span class="muted">
                                                        Periode tidak tersedia
                                                    </span>

                                                </td>

                                                <td>
                                                    Tidak dicatat
                                                </td>

                                                <td>
                                                    Tidak tersedia
                                                </td>

                                                <td>

                                                    <span class="badge {{ $paymentStatusClass }}">
                                                        {{ $paymentStatusLabel }}
                                                    </span>

                                                </td>

                                                <td>
                                                    {{ $currentUser->updated_at?->format('d M Y, H:i') ?? '—' }}
                                                </td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                                <p class="notice" style="margin: 14px;">

                                    Skema saat ini hanya menyimpan satu status dan bukti terkini
                                    pada akun pengguna, bukan histori transaksi.

                                </p>

                            @else

                                <div class="empty-state">

                                    <strong>
                                        Belum ada pembayaran untuk ditampilkan.
                                    </strong>

                                    Setelah Anda mengirim bukti, status pembayaran akan tampil di sini.

                                    @if ($currentUser->payment_status !== 'approved')

                                        <br>

                                        <a class="button"
                                            href="{{ route('payment.show') }}">

                                            Unggah bukti pembayaran

                                        </a>

                                    @endif

                                </div>

                            @endif

                        </div>

                    </section>


                    {{-- ==================== PROFIL ==================== --}}

                    <section class="section" id="profile">

                        <div class="section-heading">

                            <div>

                                <h2>Profil Saya</h2>

                                <p>
                                    Informasi akun yang sedang digunakan.
                                </p>

                            </div>

                        </div>

                        <div class="panel panel-body">

                            <div class="profile-card">

                                <div class="avatar" aria-hidden="true">

                                    {{ mb_strtoupper(mb_substr($currentUser->name, 0, 1)) }}

                                </div>

                                <div>

                                    <strong>
                                        {{ $currentUser->name }}
                                    </strong>

                                    <span>
                                        {{ $currentUser->email }}
                                    </span>

                                </div>

                            </div>

                            <p class="notice">

                                Fitur ubah profil belum tersedia pada proyek saat ini.

                            </p>

                        </div>

                    </section>

                @endif

            </div>

        </main>

    </div>


    <script>

        function confirmVerification(event, form) {

            const status = event.submitter
                ? event.submitter.value
                : '';

            const note = form.querySelector('[name="note"]');

            if (status === 'rejected' && !note.value.trim()) {

                event.preventDefault();

                window.alert(
                    'Alasan penolakan wajib diisi.'
                );

                note.focus();

                return false;
            }

            const message =
                status === 'approved'
                    ? 'Verifikasi pembayaran ini? Status yang sudah diproses tidak dapat diverifikasi ulang.'
                    : 'Tolak bukti pembayaran ini dengan alasan yang telah diisi?';

            if (!window.confirm(message)) {

                event.preventDefault();

                return false;
            }

            return true;
        }


        document
            .querySelectorAll('.nav-link[href^="#"]')
            .forEach((link) => {

                link.addEventListener('click', () => {

                    document
                        .querySelectorAll('.nav-link.active')
                        .forEach((activeLink) => {

                            activeLink.classList.remove('active');

                        });

                    link.classList.add('active');

                });

            });

    </script>

</body>

</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Penghuni - Kost Athia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; color: #0f172a; font-family: Inter, Arial, sans-serif; font-size: 14px; }
        a { color: inherit; text-decoration: none; }
        .topbar { display: flex; height: 70px; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; background: #fff; padding: 0 6%; }
        .brand { color: #2563eb; font-size: 20px; font-weight: 800; }
        .top-actions, .actions, .row-actions { display: flex; align-items: center; gap: 8px; }
        .top-actions form, .row-actions form { margin: 0; }
        .page { width: min(1180px, 92%); margin: 34px auto; }
        .heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .eyebrow { margin: 0 0 7px; color: #2563eb; font-size: 11px; font-weight: 700; letter-spacing: .09em; }
        h1 { margin: 0; font-size: 28px; letter-spacing: -.03em; }
        .subtitle { margin: 8px 0 0; color: #64748b; line-height: 1.5; }
        .button { display: inline-flex; min-height: 39px; align-items: center; justify-content: center; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; font: inherit; font-size: 12px; font-weight: 700; padding: 10px 14px; }
        .button:hover { background: #1d4ed8; }
        .button.secondary { border: 1px solid #e2e8f0; background: #fff; color: #334155; }
        .button.secondary:hover { border-color: #bfdbfe; background: #eff6ff; color: #2563eb; }
        .button.danger { background: #dc2626; }
        .button.danger:hover { background: #b91c1c; }
        .alert { margin-bottom: 16px; border: 1px solid; border-radius: 9px; padding: 12px 14px; }
        .alert.success { border-color: #bbf7d0; background: #f0fdf4; color: #166534; }
        .alert.error { border-color: #fecaca; background: #fef2f2; color: #991b1b; }
        .panel { overflow: hidden; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #fbfdff; color: #64748b; font-size: 10px; letter-spacing: .06em; padding: 14px 16px; text-transform: uppercase; white-space: nowrap; }
        td { border-top: 1px solid #f1f5f9; color: #334155; font-size: 12px; padding: 14px 16px; vertical-align: middle; }
        td strong { color: #0f172a; }
        .badge { display: inline-flex; border-radius: 999px; background: #f1f5f9; color: #475569; font-size: 10px; font-weight: 700; padding: 6px 9px; white-space: nowrap; }
        .badge.approved { background: #f0fdf4; color: #15803d; }
        .badge.rejected { background: #fef2f2; color: #b91c1c; }
        .badge.submitted { background: #eff6ff; color: #1d4ed8; }
        .empty { padding: 36px 20px; color: #64748b; line-height: 1.6; text-align: center; }
        .empty strong { display: block; margin-bottom: 6px; color: #0f172a; }
        .footer { padding: 15px; }
        .pagination { display: flex; justify-content: center; }
        @media (max-width: 760px) {
            .topbar { height: 60px; padding: 0 4%; }
            .page { width: 92%; margin: 24px auto; }
            .heading { align-items: flex-start; flex-direction: column; }
            h1 { font-size: 24px; }
            th, td { padding: 12px; }
            .row-actions { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}">Kost Athia</a>
        <nav class="top-actions" aria-label="Navigasi admin">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="button secondary" type="submit">Keluar</button>
            </form>
        </nav>
    </header>
    <main class="page">
        <div class="heading">
            <div>
                <p class="eyebrow">PENGELOLAAN / PENGHUNI</p>
                <h1>Data Penghuni</h1>
                <p class="subtitle">Kelola akun penghuni, status pembayaran, dan data akun.</p>
            </div>
            
        </div>

        @if (session('success'))
            <div class="alert success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error" role="alert">{{ $errors->first() }}</div>
        @endif

        @php
            $statusLabels = [
                'pending' => 'Belum membayar',
                'submitted' => 'Menunggu verifikasi',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
            ];
        @endphp
        <section class="panel">
            @if ($residents->isEmpty())
                <div class="empty">
                    <strong>Belum ada akun penghuni.</strong>
                    Tambahkan penghuni untuk mengelolanya di sini.
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th><th>Email</th><th>Status pembayaran</th><th>Booking</th><th>Tagihan</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($residents as $resident)
                                <tr>
                                    <td><strong>{{ $resident->name }}</strong></td>
                                    <td>{{ $resident->email }}</td>
                                    <td><span class="badge {{ $resident->payment_status }}">{{ $statusLabels[$resident->payment_status] ?? ucfirst($resident->payment_status) }}</span></td>
                                    <td>{{ $resident->bookings_count }}</td>
                                    <td>{{ $resident->tagihans_count }}</td>
                                    <td>
                                        <div class="row-actions">
                                            
                                            <a class="button secondary" href="{{ route('admin.residents.edit', $resident) }}">Edit</a>
                                            <form action="{{ route('admin.residents.destroy', $resident) }}" method="POST" onsubmit="return confirm('Hapus akun penghuni {{ $resident->name }}? Akun dengan booking atau tagihan tidak dapat dihapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button danger" type="submit">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($residents->hasPages())
                    <div class="footer">{{ $residents->links() }}</div>
                @endif
            @endif
        </section>
        <p class="subtitle">Penghuni yang memiliki riwayat booking atau tagihan tidak dapat dihapus agar riwayat transaksi tetap tersimpan.</p>
    </main>
</body>
</html>

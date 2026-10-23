<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Penghuni - Kost Athia</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; color: #0f172a; font-family: Inter, Arial, sans-serif; font-size: 14px; }
        a { color: inherit; text-decoration: none; }
        .topbar { display: flex; height: 70px; align-items: center; border-bottom: 1px solid #e2e8f0; background: #fff; padding: 0 6%; }
        .brand { color: #2563eb; font-size: 20px; font-weight: 800; }
        .page { width: min(720px, 92%); margin: 34px auto; }
        .back { display: inline-block; margin-bottom: 20px; color: #475569; font-weight: 600; }
        .eyebrow { margin: 0 0 7px; color: #2563eb; font-size: 11px; font-weight: 700; letter-spacing: .09em; }
        h1 { margin: 0; font-size: 27px; }
        .panel { margin-top: 22px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; padding: 24px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); }
        dl { margin: 0; }
        .row { display: grid; grid-template-columns: 180px 1fr; gap: 12px; border-bottom: 1px solid #f1f5f9; padding: 14px 0; }
        .row:first-child { padding-top: 0; }
        .row:last-child { border: 0; padding-bottom: 0; }
        dt { color: #64748b; font-weight: 600; }
        dd { margin: 0; overflow-wrap: anywhere; }
        .actions { display: flex; justify-content: flex-end; margin-top: 20px; }
        .button { display: inline-flex; min-height: 40px; align-items: center; justify-content: center; border-radius: 8px; background: #2563eb; color: #fff; font-size: 12px; font-weight: 700; padding: 10px 14px; }
        @media (max-width: 550px) { .row { grid-template-columns: 1fr; gap: 5px; } }
    </style>
</head>
<body>
    <header class="topbar"><a class="brand" href="{{ route('dashboard') }}">Kost Athia</a></header>
    <main class="page">
        <a class="back" href="{{ route('admin.residents.index') }}">&larr; Kembali ke Data Penghuni</a>
        <p class="eyebrow">PENGELOLAAN / PENGHUNI</p>
        <h1>Detail Penghuni</h1>
        <section class="panel">
            <dl>
                <div class="row"><dt>Nama</dt><dd>{{ $resident->name }}</dd></div>
                <div class="row"><dt>Email</dt><dd>{{ $resident->email }}</dd></div>
                <div class="row"><dt>Status pembayaran</dt><dd>{{ ucfirst($resident->payment_status) }}</dd></div>
                <div class="row"><dt>Jumlah booking</dt><dd>{{ $resident->bookings_count }}</dd></div>
                <div class="row"><dt>Jumlah tagihan</dt><dd>{{ $resident->tagihans_count }}</dd></div>
                <div class="row"><dt>Terdaftar</dt><dd>{{ $resident->created_at?->format('d M Y, H:i') ?? '—' }}</dd></div>
            </dl>
            <div class="actions"><a class="button" href="{{ route('admin.residents.edit', $resident) }}">Edit Penghuni</a></div>
        </section>
    </main>
</body>
</html>

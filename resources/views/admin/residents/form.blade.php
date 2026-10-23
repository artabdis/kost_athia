<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $resident->exists ? 'Edit' : 'Tambah' }} Penghuni - Kost Athia</title>
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
        .subtitle { margin: 8px 0 22px; color: #64748b; line-height: 1.55; }
        .panel { border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; padding: 24px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); }
        .field { margin-bottom: 17px; }
        label { display: block; margin-bottom: 7px; color: #334155; font-size: 12px; font-weight: 700; }
        input, select { width: 100%; min-height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #0f172a; font: inherit; padding: 10px 12px; }
        input:focus, select:focus { border-color: #2563eb; outline: 3px solid #bfdbfe; }
        .error { margin-top: 6px; color: #b91c1c; font-size: 11px; }
        .alert { margin-bottom: 16px; border: 1px solid #fecaca; border-radius: 9px; background: #fef2f2; color: #991b1b; padding: 12px 14px; }
        .actions { display: flex; justify-content: flex-end; gap: 9px; margin-top: 22px; }
        .button { display: inline-flex; min-height: 40px; align-items: center; justify-content: center; border: 1px solid transparent; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; font: inherit; font-size: 12px; font-weight: 700; padding: 10px 14px; }
        .button:hover { background: #1d4ed8; }
        .button.secondary { border-color: #e2e8f0; background: #fff; color: #334155; }
        @media (max-width: 550px) { .panel { padding: 17px; } }
    </style>
</head>
<body>
    <header class="topbar"><a class="brand" href="{{ route('dashboard') }}">Kost Athia</a></header>
    <main class="page">
        <a class="back" href="{{ route('admin.residents.index') }}">&larr; Kembali ke Data Penghuni</a>
        <p class="eyebrow">PENGELOLAAN / PENGHUNI</p>
        <h1>{{ $resident->exists ? 'Edit Penghuni' : 'Tambah Penghuni' }}</h1>
        <p class="subtitle">Kelola informasi akun dan status pembayaran penghuni.</p>
        @if ($errors->any())
            <div class="alert" role="alert">{{ $errors->first() }}</div>
        @endif
        <form class="panel" action="{{ $resident->exists ? route('admin.residents.update', $resident) : route('admin.residents.store') }}" method="POST">
            @csrf
            @if ($resident->exists)
                @method('PUT')
            @endif
            <div class="field">
                <label for="name">Nama</label>
                <input id="name" name="name" value="{{ old('name', $resident->name) }}" maxlength="255" required>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $resident->email) }}" maxlength="255" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password">Password{{ $resident->exists ? ' baru (opsional)' : '' }}</label>
                <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" {{ $resident->exists ? '' : 'required' }}>
                <small>{{ $resident->exists ? 'Kosongkan jika password tidak ingin diubah.' : 'Minimal 8 karakter, hanya huruf dan angka.' }}</small>
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="payment_status">Status pembayaran</label>
                <select id="payment_status" name="payment_status" required>
                    @foreach (['pending' => 'Belum membayar', 'submitted' => 'Menunggu verifikasi', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_status', $resident->payment_status ?? 'pending') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('payment_status')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <a class="button secondary" href="{{ route('admin.residents.index') }}">Batal</a>
                <button class="button" type="submit">{{ $resident->exists ? 'Simpan Perubahan' : 'Tambah Penghuni' }}</button>
            </div>
        </form>
    </main>
</body>
</html>

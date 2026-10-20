<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $room->exists ? 'Edit' : 'Tambah' }} Kamar - Kost Athia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; color: #0f172a; font-family: Inter, Arial, sans-serif; font-size: 14px; }
        a { color: inherit; text-decoration: none; }
        .topbar { display: flex; height: 70px; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; background: #fff; padding: 0 6%; }
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
        <a class="back" href="{{ route('admin.rooms.index') }}">&larr; Kembali ke Data Kamar</a>
        <p class="eyebrow">PENGELOLAAN / KAMAR</p>
        <h1>{{ $room->exists ? 'Edit Kamar' : 'Tambah Kamar' }}</h1>
        <p class="subtitle">Isi informasi yang didukung oleh skema data kamar saat ini.</p>
        @if ($errors->any())
            <div class="alert" role="alert">{{ $errors->first() }}</div>
        @endif
        <form class="panel" action="{{ $room->exists ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" method="POST">
            @csrf
            @if ($room->exists)
                @method('PUT')
            @endif
            <div class="field">
                <label for="kode_kamar">Kode kamar</label>
                <input id="kode_kamar" name="kode_kamar" value="{{ old('kode_kamar', $room->kode_kamar) }}" maxlength="255" @readonly($room->kode_kamar === 'VIP') required>
                @if ($room->kode_kamar === 'VIP')
                    <small>Kode VIP dipertahankan agar halaman dan booking kamar utama tetap terhubung.</small>
                @endif
                @error('kode_kamar')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="nama">Nama kamar</label>
                <input id="nama" name="nama" value="{{ old('nama', $room->nama) }}" maxlength="255" required>
                @error('nama')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="lokasi">Lokasi</label>
                <input id="lokasi" name="lokasi" value="{{ old('lokasi', $room->lokasi) }}" maxlength="255" required>
                @error('lokasi')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="harga">Harga sewa per bulan (Rp)</label>
                <input id="harga" name="harga" type="number" min="0" step="1" value="{{ old('harga', $room->harga) }}" required>
                @error('harga')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="status">Status kamar</label>
                <select id="status" name="status" required>
                    <option value="tersedia" @selected(old('status', $room->status ?? 'tersedia') === 'tersedia')>Tersedia</option>
                    <option value="terisi" @selected(old('status', $room->status) === 'terisi')>Terisi</option>
                    <option value="belum_dikonfirmasi" @selected(old('status', $room->status) === 'belum_dikonfirmasi')>Belum dikonfirmasi</option>
                </select>
                @error('status')<div class="error">{{ $message }}</div>@enderror
            </div>
            <p class="subtitle">Foto, tipe kamar, fasilitas, dan deskripsi belum didukung oleh kolom database yang tersedia.</p>
            <div class="actions">
                <a class="button secondary" href="{{ route('admin.rooms.index') }}">Batal</a>
                <button class="button" type="submit">{{ $room->exists ? 'Simpan Perubahan' : 'Tambah Kamar' }}</button>
            </div>
        </form>
    </main>
</body>
</html>

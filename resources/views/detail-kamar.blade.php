<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $kamar->nama }} - Kost Athia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f9fafb; color: #1f2937; font-family: 'Inter', sans-serif; }
        a { color: inherit; text-decoration: none; }
        .navbar {
            position: sticky; top: 0; z-index: 10; display: flex; height: 75px;
            align-items: center; justify-content: space-between; border-bottom: 1px solid #eee;
            background: #fff; padding: 0 7%;
        }
        .logo { color: #2563eb; font-size: 24px; font-weight: 800; }
        .nav-menu { display: flex; align-items: center; gap: 32px; font-size: 14px; font-weight: 500; }
        .nav-menu a { color: #4b5563; }
        .nav-menu a:hover, .nav-menu a.active { color: #2563eb; }
        .btn-login { padding: 10px 18px; color: #2563eb; font-size: 14px; font-weight: 600; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 7%; }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px; margin-bottom: 24px;
            color: #4b5563; font-size: 14px; font-weight: 600;
        }
        .back-link:hover { color: #2563eb; }
        .detail-wrapper {
            display: grid; grid-template-columns: 1.2fr .8fr; gap: 40px; padding: 30px;
            border: 1px solid #e5e7eb; border-radius: 16px; background: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .03);
        }
        .detail-image-box {
            position: relative; width: 100%; height: 400px; overflow: hidden;
            border-radius: 12px; background: #eff6ff;
        }
        .detail-image { width: 100%; height: 100%; object-fit: cover; }
        .room-status {
            position: absolute; top: 16px; right: 16px; padding: 7px 13px; border-radius: 20px;
            background: rgba(255, 251, 235, .96); color: #92400e; font-size: 12px; font-weight: 600;
        }
        .room-status.available { background: rgba(220, 252, 231, .96); color: #15803d; }
        .room-status.occupied { background: rgba(254, 226, 226, .96); color: #b91c1c; }
        .room-status.unconfirmed { background: rgba(255, 251, 235, .96); color: #92400e; }
        .detail-info { display: flex; flex-direction: column; }
        .detail-header { margin-bottom: 20px; }
        .detail-header h1 { margin-bottom: 6px; color: #111827; font-size: 28px; font-weight: 700; }
        .room-code { margin-bottom: 8px; color: #64748b; font-size: 13px; }
        .detail-location { color: #6b7280; font-size: 14px; }
        .price-tag {
            margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f3f4f6;
            color: #2563eb; font-size: 26px; font-weight: 700;
        }
        .price-tag span { color: #6b7280; font-size: 14px; font-weight: 400; }
        .section-title { margin-bottom: 10px; color: #111827; font-size: 16px; font-weight: 600; }
        .description { margin-bottom: 24px; color: #64748b; font-size: 14px; line-height: 1.6; }
        .notice {
            margin: auto 0 18px; border: 1px solid #bfdbfe; border-radius: 9px;
            background: #eff6ff; color: #1e40af; font-size: 12px; line-height: 1.6; padding: 12px 14px;
        }
        .btn-booking {
            display: block; width: 100%; margin-top: auto; border: 0; border-radius: 10px;
            background: #16a34a; box-shadow: 0 4px 12px rgba(22, 163, 74, .2);
            color: #fff; cursor: pointer; padding: 14px; text-align: center;
            font-size: 15px; font-weight: 600; transition: background .2s ease, transform .1s ease;
        }
        .btn-booking:hover { background: #15803d; }
        .btn-booking:active { transform: scale(.98); }
        .btn-disabled { background: #94a3b8; box-shadow: none; cursor: not-allowed; }
        .booking-dialog {
            width: min(420px, calc(100% - 32px)); margin: auto; padding: 28px; border: 0;
            border-radius: 14px; box-shadow: 0 20px 60px rgba(0, 0, 0, .25); color: #1f2937;
        }
        .booking-dialog::backdrop { background: rgba(15, 23, 42, .6); }
        .booking-dialog h2 { margin-bottom: 12px; font-size: 20px; }
        .booking-dialog p { margin-bottom: 20px; color: #4b5563; line-height: 1.6; }
        .booking-dialog-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .booking-dialog-actions a, .booking-dialog-actions button {
            border: 0; border-radius: 8px; cursor: pointer; padding: 10px 14px;
            font: inherit; font-weight: 600;
        }
        .booking-dialog-actions a { background: #2563eb; color: #fff; }
        .booking-dialog-actions button { background: #e5e7eb; color: #1f2937; }
        @media (max-width: 900px) {
            .detail-wrapper { grid-template-columns: 1fr; gap: 26px; }
            .detail-image-box { height: 300px; }
        }
        @media (max-width: 600px) {
            .navbar { height: 65px; padding: 0 5%; }
            .nav-menu { display: none; }
            .container { margin: 24px auto; padding: 0 5%; }
            .detail-wrapper { padding: 18px; }
            .detail-image-box { height: 240px; }
            .detail-header h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ url('/') }}" class="logo">Kost Athia</a>
        <div class="nav-menu">
            <a href="{{ url('/') }}">Beranda</a>
            <a href="{{ route('kamar') }}" class="active">Kamar</a>
            <a href="{{ url('/#fasilitas') }}">Fasilitas</a>
            <a href="{{ url('/#tentang') }}">Tentang</a>
            <a href="{{ url('/#kontak') }}">Kontak</a>
        </div>
        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-login">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            @endauth
        </div>
    </nav>

    <main class="container">
        <a href="{{ route('kamar') }}" class="back-link">&larr; Kembali ke daftar kamar</a>
        @php
            $status = $kamar->status;
            $statusLabels = [
                'tersedia' => 'Tersedia',
                'terisi' => 'Terisi',
                'belum_dikonfirmasi' => 'Belum dikonfirmasi',
            ];
            $statusKnown = isset($statusLabels[$status]);
        @endphp
        <section class="detail-wrapper">
            <div class="detail-image-box">
                @if ($image)
                    <img class="detail-image" src="{{ $image }}" alt="Foto {{ $kamar->nama }}">
                @endif
                <span class="room-status {{ $status === 'tersedia' ? 'available' : ($status === 'terisi' ? 'occupied' : ($status === 'belum_dikonfirmasi' ? 'unconfirmed' : '')) }}">
                    ● {{ $statusLabels[$status] ?? 'Status belum tersedia' }}
                </span>
            </div>

            <div class="detail-info">
                <header class="detail-header">
                    <p class="room-code">Kode kamar: {{ $kamar->kode_kamar }}</p>
                    <h1>{{ $kamar->nama }}</h1>
                    <p class="detail-location">📍 {{ $kamar->lokasi ?: 'Lokasi belum tersedia' }}</p>
                </header>

                <div class="price-tag">
                    @if ($kamar->harga)
                        Rp{{ number_format($kamar->harga, 0, ',', '.') }}
                    @else
                        Harga belum tersedia
                    @endif
                    <span>/ bulan</span>
                </div>

                <h2 class="section-title">Deskripsi Kamar</h2>
                <p class="description">Informasi deskripsi untuk kamar ini belum tersedia.</p>

                <h2 class="section-title">Fasilitas</h2>
                <p class="description">Informasi fasilitas kamar ini belum tersedia.</p>

                @if (! $statusKnown || $status === 'belum_dikonfirmasi')
                    <p class="notice">
                        {{ $status === 'belum_dikonfirmasi'
                            ? 'Status kamar belum dikonfirmasi. Booking belum dapat dilakukan sampai admin menetapkan ketersediaannya.'
                            : 'Status kamar belum tercatat di database. Booking belum dapat dilakukan sampai ketersediaannya dikonfirmasi.' }}
                    </p>
                @endif

                @if ($bookingEnabled)
                    @auth
                        <a href="{{ route('kamar.booking', $kamar->id) }}" class="btn-booking">Booking Kamar Ini</a>
                    @else
                        <button type="button" class="btn-booking" id="booking-button">Booking Kamar Ini</button>
                    @endauth
                @elseif ($status === 'terisi')
                    <button type="button" class="btn-booking btn-disabled" disabled>Kamar Sudah Terisi</button>
                @endif
            </div>
        </section>
    </main>

    @guest
        @if ($bookingEnabled)
            <dialog class="booking-dialog" id="booking-dialog" aria-labelledby="booking-dialog-title">
                <h2 id="booking-dialog-title">Buat akun untuk booking</h2>
                <p>Silakan membuat akun atau masuk untuk melanjutkan pemesanan kamar.</p>
                <div class="booking-dialog-actions">
                    <a href="{{ route('register') }}">Buat akun</a>
                    <a href="{{ route('login') }}">Sudah punya akun? Masuk</a>
                    <button type="button" id="close-booking-dialog">Tutup</button>
                </div>
            </dialog>
            <script>
                const bookingDialog = document.getElementById('booking-dialog');
                document.getElementById('booking-button').addEventListener('click', () => bookingDialog.showModal());
                document.getElementById('close-booking-dialog').addEventListener('click', () => bookingDialog.close());
            </script>
        @endif
    @endguest
</body>
</html>

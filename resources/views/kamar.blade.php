
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamar - Kost Athia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #1f2937;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .navbar {
            height: 75px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            border-bottom: 1px solid #eee;
            background: white;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #2563eb;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 32px;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-menu a {
            color: #4b5563;
            transition: 0.2s;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #2563eb;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-login {
            padding: 10px 18px;
            color: #2563eb;
            font-weight: 600;
        }

        .hero {
            min-height: 380px;
            display: flex;
            align-items: center;
            padding: 50px 7%;
            background: linear-gradient(135deg, #eff6ff, #ffffff);
        }

        .hero-content {
            width: 60%;
        }

        .hero h1 {
            font-size: 42px;
            line-height: 1.15;
            margin-bottom: 15px;
            color: #111827;
        }

        .hero h1 span {
            color: #2563eb;
        }

        .hero p {
            font-size: 16px;
            line-height: 1.6;
            color: #6b7280;
            max-width: 520px;
            margin-bottom: 25px;
        }

        .btn-hero-detail {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 9px;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .btn-hero-detail:hover {
            background: #1d4ed8;
        }

        .hero-image {
            width: 40%;
            display: flex;
            justify-content: center;
        }

        .hero-image img {
            width: 420px;
            height: 280px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .section {
            padding: 70px 7%;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 35px;
        }

        .section-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .section-header p {
            color: #6b7280;
            font-size: 14px;
        }

        .kamar-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .kamar-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .kamar-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }

        .kamar-image-wrapper {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
        }

        .kamar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .kamar-card:hover .kamar-image {
            transform: scale(1.05);
        }

        .kamar-status {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(220, 252, 231, 0.9);
            color: #15803d;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .kamar-content {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .kamar-content h3 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .kamar-location {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .kamar-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
            gap: 10px;
        }

        .price {
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
        }

        .price span {
            font-size: 12px;
            color: #6b7280;
            font-weight: 400;
        }

        .btn-detail {
            padding: 9px 16px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
        }

        .btn-detail:hover {
            background: #2563eb;
            color: white;
        }

        .detail-box {
            max-width: 800px;
            margin: 0 auto;
            padding: 35px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .detail-box h1 {
            font-size: 32px;
            margin: 25px 0;
            color: #111827;
        }

        .detail-box p {
            margin-bottom: 15px;
            line-height: 1.7;
            color: #4b5563;
        }

        .back-link {
            color: #2563eb;
            font-weight: 600;
        }

        .detail-price {
            font-size: 24px;
            font-weight: 800;
            color: #2563eb;
            margin: 20px 0;
        }

        @media (max-width: 900px) {
            .nav-menu {
                display: none;
            }

            .hero {
                flex-direction: column;
                gap: 30px;
            }

            .hero-content,
            .hero-image {
                width: 100%;
            }

            .hero h1 {
                font-size: 34px;
            }

            .hero-image img {
                width: 100%;
                height: auto;
            }

            .kamar-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 0 5%;
            }

            .hero,
            .section {
                padding-left: 5%;
                padding-right: 5%;
            }

            .kamar-grid {
                grid-template-columns: 1fr;
            }

            .detail-box {
                padding: 24px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <a href="{{ url('/') }}" class="logo">Kost Athia</a>

        <div class="nav-menu">
            <a href="{{ url('/') }}">Beranda</a>
            <a href="{{ url('/kamar') }}" class="active">Kamar</a>
            <a href="{{ url('/#fasilitas') }}">Fasilitas</a>
            <a href="{{ url('/#tentang') }}">Tentang</a>
            <a href="{{ url('/#kontak') }}">Kontak</a>
        </div>

        <div class="nav-buttons">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-login">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            @endauth
        </div>
    </nav>

    @if(isset($detailKamar))

        {{-- HALAMAN DETAIL KAMAR A01, B01, ATAU B02 --}}
        <section class="section" style="background: #eff6ff; min-height: 80vh;">
            <div class="detail-box">

                <a href="{{ route('kamar') }}" class="back-link">
                    &larr; Kembali ke daftar kamar
                </a>

                <h1>Detail Kamar {{ $detailKamar->kode_kamar }}</h1>

                @if($detailKamar->kode_kamar === 'A01')
                    <img
                        src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=80"
                        alt="Kamar A01"
                        style="width: 100%; height: 300px; object-fit: cover; border-radius: 12px; margin-bottom: 20px;">
                @elseif($detailKamar->kode_kamar === 'B01')
                    <img
                        src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=80"
                        alt="Kamar B01"
                        style="width: 100%; height: 300px; object-fit: cover; border-radius: 12px; margin-bottom: 20px;">
                @elseif($detailKamar->kode_kamar === 'B02')
                    <img
                        src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=80"
                        alt="Kamar B02"
                        style="width: 100%; height: 300px; object-fit: cover; border-radius: 12px; margin-bottom: 20px;">
                @endif

                <p>
                    <strong>Kode kamar:</strong>
                    {{ $detailKamar->kode_kamar }}
                </p>

                <p>
                    <strong>Lantai:</strong>
                    {{ $detailKamar->kode_kamar === 'A01' ? 'Lantai 1' : 'Lantai 2' }}
                </p>

                <p>
                    <strong>Status:</strong>
                    {{ ucfirst($detailKamar->status) }}
                </p>

                <div class="detail-price">
                    Rp{{ number_format($detailKamar->kode_kamar === 'A01' ? 750000 : 1000000, 0, ',', '.') }}
                    <span style="font-size: 14px; color: #6b7280; font-weight: 400;">/bulan</span>
                </div>

                @if($detailKamar->status === 'tersedia')
                    <a href="{{ route('kamar.booking', $detailKamar->id) }}"
                       class="btn-hero-detail">
                        Booking Kamar
                    </a>
                @else
                    <p style="color: #dc2626; font-weight: 600;">
                        Kamar ini sedang terisi.
                    </p>
                @endif

            </div>
        </section>

    @else

        {{-- HALAMAN DAFTAR KAMAR --}}
        <section class="hero">
            <div class="hero-content">
                <h1>Kamar <span>VIP</span></h1>

                <p>
                    Kamar eksklusif dengan fasilitas lengkap, luas,
                    dan kenyamanan maksimal untuk istirahat Anda.
                </p>

                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <a href="{{ url('/kamar/vip') }}" class="btn-hero-detail">
                        Detail Kamar VIP
                    </a>

                    <span style="font-size: 18px; font-weight: 700; color: #2563eb;">
                        Rp1.500.000
                        <span style="font-size: 12px; color: #6b7280; font-weight: 400;">
                            /bulan
                        </span>
                    </span>
                </div>
            </div>

            <div class="hero-image">
                <img
                    src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=900&q=80"
                    alt="Kamar VIP">
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2>Pilihan Kamar Lainnya</h2>
                    <p>Temukan berbagai opsi kamar yang tersedia sesuai kebutuhan Anda.</p>
                </div>
            </div>

            <div class="kamar-grid">

                {{-- KAMAR A01 --}}
                <div class="kamar-card">
                    <div class="kamar-image-wrapper">
                        <img
                            class="kamar-image"
                            src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800&q=80"
                            alt="Kamar A01">

                        @php
                            $roomA01 = $kamar->firstWhere('kode_kamar', 'A01');
                        @endphp

                        <span class="kamar-status">
                            {{ $roomA01 && $roomA01->status === 'tersedia' ? 'Tersedia' : 'Terisi' }}
                        </span>
                    </div>

                    <div class="kamar-content">
                        <h3>Kamar A01</h3>

                        <div class="kamar-location">📍 Lantai 1</div>

                        <div class="kamar-bottom">
                            <div class="price">
                                Rp750.000 <span>/bulan</span>
                            </div>

                            <a href="{{ url('/kamar/' . ($roomA01->id ?? 1)) }}" class="btn-detail">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>

                {{-- KAMAR B01 --}}
                <div class="kamar-card">
                    <div class="kamar-image-wrapper">
                        <img
                            class="kamar-image"
                            src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800&q=80"
                            alt="Kamar B01">

                        @php
                            $roomB01 = $kamar->firstWhere('kode_kamar', 'B01');
                        @endphp

                        <span class="kamar-status">
                            {{ $roomB01 && $roomB01->status === 'tersedia' ? 'Tersedia' : 'Terisi' }}
                        </span>
                    </div>

                    <div class="kamar-content">
                        <h3>Kamar B01</h3>

                        <div class="kamar-location">📍 Lantai 2</div>

                        <div class="kamar-bottom">
                            <div class="price">
                                Rp1.000.000 <span>/bulan</span>
                            </div>

                            <a href="{{ url('/kamar/' . ($roomB01->id ?? 2)) }}" class="btn-detail">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>

                {{-- KAMAR B02 --}}
                <div class="kamar-card">
                    <div class="kamar-image-wrapper">
                        <img
                            class="kamar-image"
                            src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800&q=80"
                            alt="Kamar B02">

                        @php
                            $roomB02 = $kamar->firstWhere('kode_kamar', 'B02');
                        @endphp

                        <span class="kamar-status">
                            {{ $roomB02 && $roomB02->status === 'tersedia' ? 'Tersedia' : 'Terisi' }}
                        </span>
                    </div>

                    <div class="kamar-content">
                        <h3>Kamar B02</h3>

                        <div class="kamar-location">📍 Lantai 2</div>

                        <div class="kamar-bottom">
                            <div class="price">
                                Rp1.000.000 <span>/bulan</span>
                            </div>

                            <a href="{{ url('/kamar/' . ($roomB02->id ?? 3)) }}" class="btn-detail">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    @endif

</body>
</html>

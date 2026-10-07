<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>KostKu - Temukan Kost Nyaman</title>

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

        /* ================= NAVBAR ================= */

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

        .nav-menu a:hover {
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

        .btn-register {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-register:hover {
            background: #1d4ed8;
        }

        /* ================= HERO ================= */

        .hero {
            min-height: 560px;
            display: flex;
            align-items: center;
            padding: 70px 7%;
            background: linear-gradient(135deg, #eff6ff, #ffffff);
        }

        .hero-content {
            width: 55%;
        }

        .hero-badge {
            display: inline-block;
            background: #dbeafe;
            color: #2563eb;
            padding: 8px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 52px;
            line-height: 1.15;
            margin-bottom: 20px;
            color: #111827;
        }

        .hero h1 span {
            color: #2563eb;
        }

        .hero p {
            font-size: 17px;
            line-height: 1.7;
            color: #6b7280;
            max-width: 570px;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 14px 24px;
            border-radius: 9px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            border: 1px solid #d1d5db;
            padding: 14px 24px;
            border-radius: 9px;
            font-weight: 600;
            background: white;
        }

        .btn-secondary:hover {
            border-color: #2563eb;
            color: #2563eb;
        }

        /* ================= HERO IMAGE ================= */

        .hero-image {
            width: 45%;
            display: flex;
            justify-content: center;
        }

        .hero-image img {
            width: 480px;
            height: 350px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }

        /* ================= SECTION ================= */

        .section {
            padding: 80px 7%;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            margin-bottom: 35px;
        }

        .section-header h2 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .section-header p {
            color: #6b7280;
        }

        .view-all {
            color: #2563eb;
            font-weight: 600;
            font-size: 14px;
        }

        /* ================= ROOM CARD ================= */

        .room-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .room-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            background: white;
            transition: 0.25s;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .room-image {
            width: 100%;
            height: 210px;
            object-fit: cover;
        }

        .room-content {
            padding: 20px;
        }

        .room-status {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .room-content h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .room-location {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .room-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            padding: 9px 14px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
        }

        /* ================= FEATURES ================= */

        .features {
            background: #f9fafb;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .feature {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #eee;
        }

        .feature-icon {
            width: 45px;
            height: 45px;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .feature h3 {
            margin-bottom: 8px;
            font-size: 16px;
        }

        .feature p {
            font-size: 13px;
            line-height: 1.6;
            color: #6b7280;
        }

        /* ================= CTA ================= */

        .cta {
            margin: 0 7% 80px;
            padding: 55px;
            background: #2563eb;
            border-radius: 20px;
            text-align: center;
            color: white;
        }

        .cta h2 {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .cta p {
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .cta a {
            display: inline-block;
            background: white;
            color: #2563eb;
            padding: 13px 24px;
            border-radius: 8px;
            font-weight: 700;
        }

        /* ================= FOOTER ================= */

        footer {
            background: #111827;
            color: white;
            padding: 40px 7%;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
        }

        .footer-logo {
            font-size: 20px;
            font-weight: 700;
        }

        .footer-text {
            color: #9ca3af;
            font-size: 13px;
            margin-top: 8px;
        }

        .copyright {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 30px;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {

            .nav-menu {
                display: none;
            }

            .hero {
                flex-direction: column;
                gap: 40px;
            }

            .hero-content,
            .hero-image {
                width: 100%;
            }

            .hero h1 {
                font-size: 40px;
            }

            .hero-image img {
                width: 100%;
            }

            .room-grid {
                grid-template-columns: 1fr 1fr;
            }

            .feature-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 0 5%;
            }

            .nav-buttons .btn-register {
                display: none;
            }

            .hero,
            .section {
                padding-left: 5%;
                padding-right: 5%;
            }

            .hero h1 {
                font-size: 34px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .room-grid,
            .feature-grid {
                grid-template-columns: 1fr;
            }

            .cta {
                margin-left: 5%;
                margin-right: 5%;
                padding: 40px 20px;
            }

            .footer-content {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar">

        <a href="{{ url('/') }}" class="logo">
            Kost Athia
        </a>

        <div class="nav-menu">
            <a href="{{ url('/') }}">Beranda</a>
            <a href="{{ url('/kamar') }}">Kamar</a>
            <a href="#fasilitas">Fasilitas</a>
            <a href="#tentang">Tentang</a>
            <a href="#kontak">Kontak</a>
        </div>

        <div class="nav-buttons">
            <a href="{{ route('login') }}" class="btn-login">
                Masuk
            </a>

            <a href="{{ route('register') }}" class="btn-register">
                Daftar
            </a>
        </div>

    </nav>


    <!-- HERO -->

    <section class="hero">

        <div class="hero-content">

            <div class="hero-badge">
                Kost nyaman untuk hidup lebih nyaman
            </div>

            <h1>
                Temukan Kamar Kost
                <span>Impianmu</span>
            </h1>

            <p>
                Temukan kamar kost yang nyaman, aman, dan sesuai
                dengan kebutuhanmu. Lihat kamar dan fasilitasnya
                terlebih dahulu tanpa perlu login.
            </p>

            <div class="hero-buttons">

                <a href="{{ url('/kamar') }}" class="btn-primary">
                    Lihat Kamar
                </a>

                <a href="#fasilitas" class="btn-secondary">
                    Lihat Fasilitas
                </a>

            </div>

        </div>


        <div class="hero-image">

            <img
                src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=900&q=80"
                alt="Kamar Kost"
            >

        </div>

    </section>


    <!-- ROOMS -->

    <section class="section">

        <div class="section-header">

            <div>
                <h2>Kamar Tersedia</h2>

                <p>
                    Pilih kamar yang sesuai dengan kebutuhanmu.
                </p>
            </div>

            <a href="{{ url('/kamar') }}" class="view-all">
                Lihat Semua →
            </a>

        </div>


        <div class="room-grid">

            <!-- ROOM 1 -->

            <div class="room-card">

                <img
                    class="room-image"
                    src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800&q=80"
                    alt="Kamar A01"
                >

                <div class="room-content">

                    <span class="room-status">
                        Tersedia
                    </span>

                    <h3>Kamar A01</h3>

                    <div class="room-location">
                        📍 Lantai 1
                    </div>

                    <div class="room-bottom">

                        <div class="price">
                            Rp750.000
                            <span>/bulan</span>
                        </div>

                        <a
                            href="{{ url('/kamar/1') }}"
                            class="btn-detail"
                        >
                            Detail
                        </a>

                    </div>

                </div>

            </div>


            <!-- ROOM 2 -->

            <div class="room-card">

                <img
                    class="room-image"
                    src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80"
                    alt="Kamar B01"
                >

                <div class="room-content">

                    <span class="room-status">
                        Tersedia
                    </span>

                    <h3>Kamar B01</h3>

                    <div class="room-location">
                        📍 Lantai 2
                    </div>

                    <div class="room-bottom">

                        <div class="price">
                            Rp1.000.000
                            <span>/bulan</span>
                        </div>

                        <a
                            href="{{ url('/kamar/2') }}"
                            class="btn-detail"
                        >
                            Detail
                        </a>

                    </div>

                </div>

            </div>


            <!-- ROOM 3 -->

            <div class="room-card">

                <img
                    class="room-image"
                    src="https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80"
                    alt="Kamar B02"
                >

                <div class="room-content">

                    <span class="room-status">
                        Tersedia
                    </span>

                    <h3>Kamar B02</h3>

                    <div class="room-location">
                        📍 Lantai 2
                    </div>

                    <div class="room-bottom">

                        <div class="price">
                            Rp1.000.000
                            <span>/bulan</span>
                        </div>

                        <a
                            href="{{ url('/kamar/3') }}"
                            class="btn-detail"
                        >
                            Detail
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- FEATURES -->

    <section class="section features" id="fasilitas">

        <div class="section-header">

            <div>

                <h2>Fasilitas Kost</h2>

                <p>
                    Fasilitas yang tersedia untuk menunjang kenyamanan penghuni.
                </p>

            </div>

        </div>


        <div class="feature-grid">

            <div class="feature">

                <div class="feature-icon">
                    📶
                </div>

                <h3>Wi-Fi</h3>

                <p>
                    Internet tersedia untuk menunjang aktivitas sehari-hari.
                </p>

            </div>


            <div class="feature">

                <div class="feature-icon">
                    🚿
                </div>

                <h3>Kamar Mandi</h3>

                <p>
                    Kamar mandi bersih dan nyaman untuk penghuni.
                </p>

            </div>


            <div class="feature">

                <div class="feature-icon">
                    🅿️
                </div>

                <h3>Area Parkir</h3>

                <p>
                    Tersedia area parkir kendaraan bagi penghuni.
                </p>

            </div>


            <div class="feature">

                <div class="feature-icon">
                    🔒
                </div>

                <h3>Keamanan</h3>

                <p>
                    Lingkungan kost yang aman dan nyaman.
                </p>

            </div>

        </div>

    </section>


    <!-- CTA -->

    <section class="cta">

        <h2>Sudah menemukan kamar yang cocok?</h2>

        <p>
            Lihat kamar yang tersedia dan pilih kamar yang sesuai dengan kebutuhanmu.
        </p>

        <a href="{{ url('/kamar') }}">
            Cari Kamar Sekarang
        </a>

    </section>


    <!-- FOOTER -->

    <footer id="kontak">

        <div class="footer-content">

            <div>

                <div class="footer-logo">
                    KOST ATHIA
                </div>

                <div class="footer-text">
                    Tempat menemukan kost nyaman untukmu.
                </div>

            </div>

            <div class="footer-text">
                Jember, Jawa Timur
            </div>

        </div>

        <div class="copyright">
            © {{ date('Y') }} KostKu. All rights reserved.
        </div>

    </footer>

</body>
</html>

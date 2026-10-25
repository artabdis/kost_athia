<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kamar VIP - Kost Athia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
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

        /* DETAIL CONTAINER */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 7%;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 24px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #2563eb;
        }

        .detail-wrapper {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            background: white;
            padding: 30px;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
        }

        .detail-image-box {
            position: relative;
            width: 100%;
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
        }

        .detail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .room-status {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(220, 252, 231, 0.95);
            backdrop-filter: blur(4px);
            color: #15803d;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .room-status.unavailable {
            background: rgba(254, 226, 226, 0.95);
            color: #b91c1c;
        }

        .detail-info {
            display: flex;
            flex-direction: column;
        }

        .detail-header {
            margin-bottom: 20px;
        }

        .detail-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .detail-location {
            color: #6b7280;
            font-size: 14px;
        }

        .price-tag {
            font-size: 26px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f3f4f6;
        }

        .price-tag span {
            font-size: 14px;
            color: #6b7280;
            font-weight: 400;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
        }

        .description {
            font-size: 14px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 24px;
        }

        .facilities-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 30px;
        }

        .facility-item {
            font-size: 13px;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* TOMBOL BOOKING WARNA HIJAU */
        .btn-booking {
            display: block;
            width: 100%;
            text-align: center;
            background: #16a34a;
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
            margin-top: auto;
            border: none;
            cursor: pointer;
        }

        .btn-booking:hover {
            background: #15803d;
        }

        .btn-booking:active {
            transform: scale(0.98);
        }

        .btn-booking:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            box-shadow: none;
        }

        .booking-dialog {
            max-width: 420px;
            margin: auto;
            padding: 28px;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            color: #1f2937;
        }

        .booking-dialog::backdrop {
            background: rgba(15, 23, 42, 0.6);
        }

        .booking-dialog h2 {
            margin-bottom: 12px;
        }

        .booking-dialog p {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #4b5563;
        }

        .booking-dialog-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .booking-dialog-actions a,
        .booking-dialog-actions button {
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font: inherit;
            font-weight: 600;
        }

        .booking-dialog-actions a {
            background: #2563eb;
            color: white;
        }

        .booking-dialog-actions button {
            background: #e5e7eb;
            color: #1f2937;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .detail-wrapper {
                grid-template-columns: 1fr;
            }

            .detail-image-box {
                height: 300px;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 0 5%;
            }

            .nav-menu {
                gap: 15px;
            }

            .container {
                padding: 0 5%;
                margin: 20px auto;
            }

            .detail-wrapper {
                padding: 20px;
            }

            .detail-header h1 {
                font-size: 24px;
            }

            .price-tag {
                font-size: 22px;
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
            <a href="{{ url('/') }}">
                Beranda
            </a>

            <a href="{{ url('/kamar') }}" class="active">
                Kamar
            </a>

            <a href="{{ url('/#fasilitas') }}">
                Fasilitas
            </a>

            <a href="{{ url('/#tentang') }}">
                Tentang
            </a>

            <a href="{{ url('/#kontak') }}">
                Kontak
            </a>
        </div>

        <div class="nav-buttons">

            @auth
                <a href="{{ route('dashboard') }}" class="btn-login">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-login">
                    Masuk
                </a>
            @endauth

        </div>

    </nav>


    <!-- MAIN CONTENT -->
    <div class="container">

        <a href="{{ url('/kamar') }}" class="back-link">
            &larr; Kembali ke Daftar Kamar
        </a>


        <div class="detail-wrapper">

            <!-- SISI KIRI: GAMBAR & STATUS -->
            @php
                $vipStatus = $bookingEnabled
                    ? ($room?->status ?? 'belum tersedia')
                    : ($room?->status === 'tersedia' ? 'sedang dipesan' : ($room?->status ?? 'belum tersedia'));
            @endphp

            <div class="detail-image-box">

                <!-- GAMBAR KAMAR VIP -->
                <img
                    class="detail-image"
                    src="{{ asset('images/vip.jpg') }}"
                    alt="Kamar VIP Kost Athia"
                >

                <span class="room-status {{ $vipStatus === 'tersedia' ? '' : 'unavailable' }}">
                    ● {{ ucfirst($vipStatus) }}
                </span>

            </div>


            <!-- SISI KANAN: INFORMASI & BOOKING -->
            <div class="detail-info">

                <div class="detail-header">

                    <h1>
                        {{ $room?->nama ?? 'Kamar VIP' }}
                    </h1>

                    <div class="detail-location">
                        📍 {{ $room?->lokasi ?? 'Lantai Utama (Akses Eksklusif)' }}
                    </div>

                </div>


                <div class="price-tag">
                    Rp{{ number_format($room?->harga ?? 1500000, 0, ',', '.') }}
                    <span>/ bulan</span>
                </div>


                <div class="section-title">
                    Deskripsi Kamar
                </div>

                <p class="description">
                    Kamar eksklusif tipe VIP dengan ukuran lebih luas,
                    pencahayaan alami yang baik, serta ventilasi optimal.
                    Sangat cocok bagi Anda yang menginginkan privasi dan
                    kenyamanan tingkat tinggi selama beristirahat.
                </p>


                <div class="section-title">
                    Fasilitas Utama
                </div>

                <div class="facilities-list">

                    <div class="facility-item">
                        🛏️ Kasur Springbed Queen Size
                    </div>

                    <div class="facility-item">
                        ❄️ AC (Air Conditioner)
                    </div>

                    <div class="facility-item">
                        🚿 Kamar Mandi Dalam (Water Heater)
                    </div>

                    <div class="facility-item">
                        🪑 Meja & Kursi Kerja
                    </div>

                    <div class="facility-item">
                        📶 Wi-Fi Cepat (Dedicated)
                    </div>

                    <div class="facility-item">
                        📦 Lemari Pakaian Besar
                    </div>

                </div>


                <!-- TOMBOL BOOKING -->
                @if ($bookingEnabled)

                    @auth

                        <form action="{{ route('kamar.vip.booking') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-booking">Booking</button>
                        </form>

                    @else

                        <button
                            type="button"
                            class="btn-booking"
                            id="booking-button"
                        >
                            Booking
                        </button>

                    @endauth

                @else

                    <button
                        type="button"
                        class="btn-booking"
                        disabled
                    >
                        Kamar tidak tersedia untuk booking
                    </button>

                @endif

            </div>

        </div>

    </div>


    <!-- DIALOG UNTUK USER YANG BELUM LOGIN -->
    @guest

        @if ($bookingEnabled)

            <dialog
                class="booking-dialog"
                id="booking-dialog"
                aria-labelledby="booking-dialog-title"
            >

                <h2 id="booking-dialog-title">
                    Buat akun untuk booking
                </h2>

                <p>
                    Silahkan membuat akun untuk melanjutkan
                    pemesanan kamar.
                </p>

                <div class="booking-dialog-actions">

                    <form action="{{ route('kamar.vip.booking') }}" method="POST">
                        @csrf
                        <button type="submit">Buat akun</button>
                    </form>

                    <!-- <a href="{{ route('login') }}">
                        Sudah punya akun? Masuk
                    </a> -->

                    <button
                        type="button"
                        id="close-booking-dialog"
                    >
                        Tutup
                    </button>

                </div>

            </dialog>


            <script>
                const bookingDialog =
                    document.getElementById('booking-dialog');

                const bookingButton =
                    document.getElementById('booking-button');

                const closeBookingDialog =
                    document.getElementById('close-booking-dialog');

                if (bookingButton) {
                    bookingButton.addEventListener('click', () => {
                        bookingDialog.showModal();
                    });
                }

                if (closeBookingDialog) {
                    closeBookingDialog.addEventListener('click', () => {
                        bookingDialog.close();
                    });
                }
            </script>

        @endif

    @endguest

</body>

</html>

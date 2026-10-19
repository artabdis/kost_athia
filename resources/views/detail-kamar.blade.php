<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $kamar->nama }} - Kost Athia</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .navbar {
            background: white;
            padding: 25px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }

        .back {
            display: inline-block;
            margin: 30px 8%;
            color: #334155;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            width: 84%;
            max-width: 1100px;
            margin: 0 auto 50px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }

        .badge {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .badge-full {
            background: #fee2e2;
            color: #dc2626;
        }

        h1 {
            font-size: 38px;
            margin-bottom: 12px;
        }

        .kode {
            color: #64748b;
            margin-bottom: 10px;
        }

        .lokasi {
            color: #475569;
            margin-bottom: 25px;
        }

        .harga {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin: 25px 0;
        }

        .harga span {
            font-size: 16px;
            font-weight: normal;
            color: #64748b;
        }

        .status {
            margin: 20px 0;
            font-size: 17px;
        }

        .btn {
            display: inline-block;
            padding: 14px 30px;
            background: #16a34a;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background: #15803d;
        }

        .btn-disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .login-info {
            margin-top: 15px;
            color: #64748b;
        }

        .login-info a {
            color: #2563eb;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="logo">Kost Athia</div>

        <a href="{{ route('kamar') }}" style="text-decoration:none;color:#2563eb;">
            Daftar Kamar
        </a>
    </nav>

    <a href="{{ route('kamar') }}" class="back">
        ← Kembali ke Daftar Kamar
    </a>

    <div class="container">

        <div class="card">

            @if ($kamar->status === 'tersedia')
                <div class="badge">
                    ● Tersedia
                </div>
            @else
                <div class="badge badge-full">
                    ● Sudah Terisi
                </div>
            @endif

            <h1>{{ $kamar->nama }}</h1>

            <div class="kode">
                Kode Kamar: <strong>{{ $kamar->kode_kamar }}</strong>
            </div>

            <div class="lokasi">
                📍 {{ $kamar->lokasi }}
            </div>

            <div class="harga">
                Rp{{ number_format($kamar->harga, 0, ',', '.') }}
                <span>/bulan</span>
            </div>

            <div class="status">
                Status:
                <strong>
                    {{ ucfirst($kamar->status) }}
                </strong>
            </div>

            @if ($kamar->status === 'tersedia')

                @auth

                    <a
                        href="{{ route('kamar.booking', $kamar->id) }}"
                        class="btn"
                    >
                        Booking Kamar Ini
                    </a>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="btn"
                    >
                        Login untuk Booking
                    </a>

                    <div class="login-info">
                        Belum punya akun?
                        <a href="{{ route('register') }}">
                            Daftar sekarang
                        </a>
                    </div>

                @endauth

            @else

                <button class="btn btn-disabled" disabled>
                    Kamar Sudah Terisi
                </button>

            @endif

        </div>

    </div>

</body>
</html>
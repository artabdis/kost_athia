<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .navbar {
            background: #1e293b;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            font-size: 22px;
        }

        .navbar span {
            color: #cbd5e1;
            font-size: 14px;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome {
            margin-bottom: 30px;
        }

        .welcome h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #64748b;
        }

        .role {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin-top: 12px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .card h3 {
            margin-bottom: 12px;
            font-size: 18px;
        }

        .card p {
            color: #64748b;
            line-height: 1.6;
            font-size: 14px;
        }

        .card a {
            display: inline-block;
            margin-top: 18px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .admin-title {
            margin-bottom: 20px;
        }

        .logout {
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 7px;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 18px 20px;
                gap: 12px;
            }

            .navbar h2 {
                font-size: 18px;
            }

            .container {
                margin-top: 25px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <h2>Dashboard</h2>

        <div>
            <span>{{ Auth::user()->name }}</span>

            <form action="{{ route('logout') }}"
                  method="POST"
                  style="display:inline; margin-left:12px;">
                @csrf
                <button type="submit" class="logout">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="container">

        <section class="welcome">
            <h1>
                Selamat datang, {{ Auth::user()->name }}!
            </h1>

            <p>
                Selamat datang di halaman dashboard aplikasi.
            </p>

            <span class="role">
                Role: {{ ucfirst(Auth::user()->role) }}
            </span>
        </section>

        {{-- DASHBOARD ADMIN --}}
        @if (Auth::user()->role === 'admin')

            <h2 class="admin-title">Dashboard Administrator</h2>

            <div class="cards">

                <div class="card">
                    <h3>Kelola Pengguna</h3>
                    <p>
                        Mengelola akun, data pengguna,
                        dan hak akses aplikasi.
                    </p>
                    <a href="#">Kelola pengguna &rarr;</a>
                </div>

                <div class="card">
                    <h3>Kelola Kamar</h3>
                    <p>
                        Menambah, mengubah, dan menghapus
                        data kamar.
                    </p>
                    <a href="{{ route('kamar') }}">
                        Kelola kamar &rarr;
                    </a>
                </div>

                <div class="card">
                    <h3>Laporan</h3>
                    <p>
                        Melihat dan mengelola laporan
                        aktivitas aplikasi.
                    </p>
                    <a href="#">Lihat laporan &rarr;</a>
                </div>

            </div>

        {{-- DASHBOARD USER --}}
        @elseif (Auth::user()->role === 'user')

            <h2 class="admin-title">Dashboard Pengguna</h2>

            <div class="cards">

                <div class="card">
                    <h3>Daftar Kamar</h3>
                    <p>
                        Lihat informasi dan daftar kamar
                        yang tersedia.
                    </p>
                    <a href="{{ route('kamar') }}">
                        Lihat kamar &rarr;
                    </a>
                </div>

                <div class="card">
                    <h3>Profil Saya</h3>
                    <p>
                        Informasi akun Anda:
                        {{ Auth::user()->email }}
                    </p>
                </div>

            </div>

        @else

            <div class="card">
                <h3>Role tidak dikenali</h3>
                <p>
                    Silakan hubungi administrator
                    untuk mendapatkan bantuan.
                </p>
            </div>

        @endif

        <form action="{{ route('logout') }}" method="POST">
    @csrf

    <button type="submit" class="btn-logout">
        Logout
    </button>
    </form>

    <style>
        .btn-logout {
            background-color: #dc2626;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .btn-logout:hover {
            background-color: #b91c1c;
        }
    </style>                                            
    </main>

</body>
</html>
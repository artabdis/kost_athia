<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Pembayaran - Kost Athia</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .wrap {
            max-width: 620px;
            margin: 50px auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 4px 18px #0000000d;
        }

        h1 {
            margin-top: 0;
        }

        .muted {
            color: #64748b;
            line-height: 1.6;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin: 14px 0;
        }

        .info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        input,
        button {
            font: inherit;
        }

        input[type=file] {
            display: block;
            margin: 15px 0;
            padding: 12px;
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }

        .btn {
            background: #2563eb;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 12px 16px;
            cursor: pointer;
            font-weight: bold;
        }

        .logout {
            background: transparent;
            border: 0;
            color: #2563eb;
            cursor: pointer;
            margin-top: 18px;
        }
    </style>
</head>

<body>
    <main class="wrap">
        <div class="card">
            <h1>Pembayaran</h1>
            <p>Halo, {{ Auth::user()->name }}. Silakan kirim bukti pembayaran dan tunggu verifikasi admin. Dashboard
                dapat diakses setelah pembayaran disetujui.</p>

            @if (session('info'))
                <div class="alert info">{{ session('info') }}</div>
            @endif
            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert error">{{ $errors->first() }}</div>
            @endif

            <p><strong>Status saat ini:</strong>
                @switch(Auth::user()->payment_status)
                    @case('submitted')
                        Menunggu verifikasi
                    @break

                    @case('approved')
                        Lunas / disetujui
                    @break

                    @case('rejected')
                        Ditolak
                    @break

                    @default
                        Belum mengunggah
                @endswitch
            </p>

            @if (Auth::user()->payment_note)
                <p class="alert error">Catatan admin: {{ Auth::user()->payment_note }}</p>
            @endif

            @if (Auth::user()->payment_status === 'submitted')
                <p class="muted">Bukti pembayaran sudah dikirim. Silakan tunggu pemeriksaan admin.</p>
            @else
                <form action="{{ route('payment.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="payment_proof"><strong>Unggah bukti pembayaran</strong> (JPG, PNG, WEBP; maksimal 5
                        MB)</label>
                    <input id="payment_proof" type="file" name="payment_proof"
                        accept="image/jpeg,image/png,image/webp" required>
                    <button class="btn" type="submit">Kirim Bukti Pembayaran</button>
                </form>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="logout" type="submit">Logout</button>
            </form>
        </div>
    </main>
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pembayaran Tagihan - Kost Athia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            color: #1e293b;
        }

        .navbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            color: #2563eb;
        }

        .back {
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        .container {
            width: min(900px, 92%);
            margin: 40px auto;
        }

        .title {
            margin-bottom: 25px;
        }

        .title h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .title p {
            margin: 0;
            color: #64748b;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.06);
            border: 1px solid #e5e7eb;
        }

        .card h2 {
            margin-top: 0;
            margin-bottom: 22px;
            font-size: 18px;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 13px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .detail:last-child {
            border-bottom: none;
        }

        .label {
            color: #64748b;
            font-size: 14px;
        }

        .value {
            font-size: 14px;
            font-weight: 600;
            text-align: right;
        }

        .nominal {
            font-size: 25px;
            font-weight: 700;
            color: #2563eb;
            margin: 5px 0 20px;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            background: #fff7ed;
            color: #c2410c;
            font-size: 12px;
            font-weight: 700;
        }

        .upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 25px;
            text-align: center;
            background: #f8fafc;
            margin-bottom: 18px;
        }

        .upload-box p {
            margin: 0 0 12px;
            color: #64748b;
            font-size: 13px;
        }

        input[type="file"] {
            width: 100%;
            font-size: 13px;
        }

        .button {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 13px;
            background: #2563eb;
            color: white;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .note {
            margin-top: 15px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 13px;
            line-height: 1.6;
        }

        .warning {
            margin-top: 15px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #fff7ed;
            color: #9a3412;
            font-size: 13px;
            line-height: 1.6;
        }

        .error {
            margin-bottom: 18px;
            padding: 13px 15px;
            border-radius: 10px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 13px;
        }

        .success {
            margin-bottom: 18px;
            padding: 13px 15px;
            border-radius: 10px;
            background: #f0fdf4;
            color: #166534;
            font-size: 13px;
        }

        @media (max-width: 700px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 0 5%;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">

        <div class="brand">
            Kost Athia
        </div>

        <a href="{{ route('dashboard') }}" class="back">
            ← Kembali ke Dashboard
        </a>

    </nav>


    <main class="container">

        <div class="title">

            <h1>
                {{ Auth::user()->role === 'admin' ? 'Detail Tagihan' : 'Pembayaran Tagihan' }}
            </h1>

            <p>
                {{ Auth::user()->role === 'admin'
                    ? 'Periksa informasi tagihan dan status pembayaran penghuni.'
                    : 'Periksa detail tagihan Anda sebelum mengunggah bukti pembayaran.' }}
            </p>

        </div>


        @if (session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif


        @if ($errors->any())

            <div class="error">

                <strong>
                    Terjadi kesalahan:
                </strong>

                <ul style="margin: 8px 0 0 18px;">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <div class="grid">


            {{--DETAIL TAGIHAN--}}

            <div class="card">

                <h2>
                    Detail Tagihan
                </h2>

                <div class="detail">

                    <span class="label">
                        Nomor Tagihan
                    </span>

                    <span class="value">
                        #{{ $tagihan->id }}
                    </span>

                </div>


                @if (Auth::user()->role === 'admin')

                    <div class="detail">

                        <span class="label">
                            Penghuni
                        </span>

                        <span class="value">
                            {{ $tagihan->user?->name ?? 'Pengguna tidak tersedia' }}
                            ({{ $tagihan->user?->email ?? '—' }})
                        </span>

                    </div>

                @endif

                <div class="detail">

                    <span class="label">
                        Jenis Pembayaran
                    </span>

                    <span class="value">
                        {{ $tagihan->jenis_pembayaran ?: '—' }}
                    </span>

                </div>


                <div class="detail">

                    <span class="label">
                        Nomor Kamar
                    </span>

                    <span class="value">
                        {{ $tagihan->room?->kode_kamar ?? '-' }}
                    </span>

                </div>

                <div class="detail">

                    <span class="label">
                        Status Penghuni
                    </span>

                    <span class="value">
                        @if ($statusPenghuni === 'approved')
                            Disetujui
                        @else
                            Menunggu persetujuan
                        @endif
                    </span>

                </div>


                <div class="detail">

                    <span class="label">
                        Periode
                    </span>

                    <span class="value">
                        {{ $tagihan->periode ?: '—' }}
                    </span>

                </div>


                <div class="detail">

                    <span class="label">
                        Jatuh Tempo
                    </span>

                    <span class="value">
                        {{ $tagihan->jatuh_tempo?->format('d F Y') ?? '-' }}
                    </span>

                </div>


                <div style="margin-top: 22px;">

                    <div class="label">
                        Total Tagihan
                    </div>

                    <div class="nominal">
                        @if ($tagihan->nominal !== null)
                            Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                        @else
                            —
                        @endif
                    </div>


                    @if ($tagihan->status === 'lunas')

                        <span
                            class="status"
                            style="background:#f0fdf4; color:#166534;">
                            Lunas
                        </span>

                    @elseif ($tagihan->status === 'ditolak')

                        <span
                            class="status"
                            style="background:#fef2f2; color:#b91c1c;">
                            Ditolak
                        </span>

                    @elseif ($tagihan->status === 'menunggu_verifikasi')

                        <span class="status">
                            Menunggu Verifikasi
                        </span>

                    @elseif ($tagihan->status === 'belum_bayar')

                        <span class="status">
                            Belum Bayar
                        </span>

                    @else

                        <span class="status">
                            {{ ucwords(str_replace('_', ' ', $tagihan->status ?: 'Status tidak tersedia')) }}
                        </span>

                    @endif


                    @if (Auth::user()->role === 'admin' && $tagihan->bukti_pembayaran)

                        <a class="button"
                            href="{{ route('admin.tagihan.proof', $tagihan) }}"
                            target="_blank"
                            rel="noopener">
                            Lihat Bukti Pembayaran
                        </a>

                    @endif

                </div>


                @if ($tagihan->status === 'ditolak' && $tagihan->catatan)

                    <div class="warning">

                        <strong>
                            Catatan Admin:
                        </strong>

                        <br>

                        {{ $tagihan->catatan }}

                    </div>

                @endif

            </div>


            {{--
                UPLOAD BUKTI
        = --}}

            @if (Auth::user()->role === 'admin')

                <div class="card">

                    <h2>Verifikasi Tagihan</h2>

                    @if ($tagihan->status === 'menunggu_verifikasi' && $tagihan->bukti_pembayaran)

                        <p>Bukti pembayaran menunggu pemeriksaan admin.</p>

                        <form action="{{ route('admin.tagihan.verify', $tagihan) }}" method="POST">
                            @csrf

                            <label for="catatan">Catatan (wajib jika pembayaran ditolak)</label>
                            <textarea id="catatan" name="catatan" maxlength="1000">{{ old('catatan') }}</textarea>

                            <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
                                <button class="button" type="submit" name="status" value="lunas">
                                    Setujui Pembayaran
                                </button>
                                <button class="button" type="submit" name="status" value="ditolak">
                                    Tolak Pembayaran
                                </button>
                            </div>
                        </form>

                    @else

                        <p>Status tagihan saat ini: {{ ucwords(str_replace('_', ' ', $tagihan->status ?: 'tidak tersedia')) }}.</p>

                    @endif

                    <a class="button" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>

                </div>

            @else

            <div class="card">

                <h2>
                    Upload Bukti Pembayaran
                </h2>


                @if (
                    $tagihan->status === 'belum_bayar'
                    || $tagihan->status === 'ditolak'
                )

                    @if ($tagihan->status === 'ditolak')

                        <div class="warning">

                            Bukti pembayaran sebelumnya ditolak oleh admin.
                            Silakan periksa catatan admin lalu upload bukti
                            pembayaran yang benar.

                        </div>

                    @endif


                    <form
                        action="{{ route('tagihan.bayar', $tagihan->id) }}"
                        method="POST"
                        enctype="multipart/form-data">

                        @csrf


                        <div class="upload-box">

                            <p>
                                Pilih foto atau screenshot bukti pembayaran.
                            </p>

                            <input
                                type="file"
                                name="bukti_pembayaran"
                                accept=".jpg,.jpeg,.png,.webp"
                                required>

                        </div>


                        <button
                            type="submit"
                            class="button">

                            Upload Bukti Baru

                        </button>

                    </form>


                    <div class="note">

                        Format yang diperbolehkan:
                        JPG, JPEG, PNG, atau WEBP.

                        <br>

                        Maksimal ukuran file 5 MB.

                    </div>


                @elseif ($tagihan->status === 'menunggu_verifikasi')

                    <div class="note">

                        Bukti pembayaran Anda sudah dikirim dan sedang menunggu
                        verifikasi dari admin.

                    </div>


                @elseif ($tagihan->status === 'lunas')

                    <div class="success">

                        Tagihan ini sudah lunas.

                    </div>


                @else

                    <div class="error">

                        Status tagihan:
                        {{ ucwords(str_replace('_', ' ', $tagihan->status)) }}

                    </div>

                @endif

            </div>

            @endif

        </div>

    </main>

</body>

</html>

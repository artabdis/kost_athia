<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Keluhan - Kost Athia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f8fc;
            color: #1f2937;
            min-height: 100vh;
        }

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            color: #2563eb;
        }

        .back-link {
            text-decoration: none;
            color: #2563eb;
            font-size: 14px;
            font-weight: 600;
        }

        .container {
            width: min(1000px, 90%);
            margin: 40px auto;
        }

        .title {
            margin-bottom: 24px;
        }

        .title h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .title p {
            color: #6b7280;
            font-size: 14px;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 5px 20px rgba(15, 23, 42, 0.06);
            border: 1px solid #e5e7eb;
        }

        .card h2 {
            font-size: 18px;
            margin-bottom: 16px;
        }

        textarea {
            width: 100%;
            min-height: 140px;
            resize: vertical;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 14px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }

        textarea:focus,
        select:focus {
            border-color: #2563eb;
        }

        .file-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 12px;
            font-family: inherit;
            font-size: 14px;
            background: #ffffff;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-help {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #6b7280;
        }

        .button {
            margin-top: 4px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .error-list {
            margin: 8px 0 0 18px;
        }

        .complaint {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            margin-top: 16px;
        }

        .complaint:first-child {
            margin-top: 0;
        }

        .complaint-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
        }

        .date {
            font-size: 12px;
            color: #6b7280;
        }

        .user-info {
            margin-bottom: 14px;
        }

        .user-name {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .user-email {
            font-size: 12px;
            color: #6b7280;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .status-diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-selesai {
            background: #dcfce7;
            color: #166534;
        }

        .complaint-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .complaint-text {
            line-height: 1.7;
            white-space: pre-line;
            font-size: 14px;
        }

        .complaint-photo {
            margin-top: 16px;
        }

        .complaint-photo img {
            width: 100%;
            max-width: 450px;
            max-height: 350px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            display: block;
        }

        .response {
            margin-top: 16px;
            padding: 14px;
            border-radius: 10px;
            background: #f5f8fc;
        }

        .response strong {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .response p {
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-line;
        }

        .admin-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .admin-form select {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 12px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            background: #ffffff;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #6b7280;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 16px 5%;
            }

            .container {
                width: 92%;
                margin: 25px auto;
            }

            .card {
                padding: 18px;
            }

            .complaint-top {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="brand">Kost Athia</div>

        <a href="{{ route('dashboard') }}" class="back-link">
            ← Kembali ke Dashboard
        </a>
    </nav>

    <main class="container">

        <div class="title">

            @if (Auth::user()->role === 'admin')
                <h1>Keluhan Penghuni</h1>
                <p>Lihat keluhan penghuni dan berikan tanggapan.</p>
            @else
                <h1>Keluhan</h1>
                <p>Sampaikan keluhan atau kendala yang kamu alami selama tinggal di Kost Athia.</p>
            @endif

        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Terjadi kesalahan:</strong>

                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- =====================================================
             BAGIAN PENGHUNI
        ====================================================== --}}

        @if (Auth::user()->role === 'user')

            <div class="card">

                <h2>Sampaikan Keluhan</h2>

                <form
                    action="{{ route('keluhan.store') }}"
                    method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    <div class="form-group">

                        <label class="form-label">
                            Deskripsi Keluhan
                        </label>

                        <textarea
                            name="isi_keluhan"
                            placeholder="Tuliskan keluhan kamu di sini..."
                            required>{{ old('isi_keluhan') }}</textarea>

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            Foto Keluhan
                        </label>

                        <input
                            type="file"
                            name="foto"
                            class="file-input"
                            accept=".jpg,.jpeg,.png,.webp">

                        <span class="form-help">
                            Foto bersifat opsional. Format JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.
                        </span>

                    </div>

                    <button type="submit" class="button">
                        Kirim Keluhan
                    </button>

                </form>

            </div>

            <div class="card">

                <h2>Riwayat Keluhan</h2>

                @forelse ($keluhans as $keluhan)

                    <div class="complaint">

                        <div class="complaint-top">

                            <span class="date">
                                {{ $keluhan->created_at->format('d M Y, H:i') }}
                            </span>

                            <span class="status status-{{ $keluhan->status }}">

                                @if ($keluhan->status === 'menunggu')
                                    Menunggu
                                @elseif ($keluhan->status === 'diproses')
                                    Diproses
                                @else
                                    Selesai
                                @endif

                            </span>

                        </div>

                        <span class="complaint-label">
                            Keluhan
                        </span>

                        <div class="complaint-text">
                            {{ $keluhan->isi_keluhan }}
                        </div>


                        @if ($keluhan->foto)

                            <div class="complaint-photo">

                                <span class="complaint-label">
                                    Foto Keluhan
                                </span>

                                <img
                                    src="{{ asset('storage/' . $keluhan->foto) }}"
                                    alt="Foto keluhan">

                            </div>

                        @endif


                        @if ($keluhan->tanggapan)

                            <div class="response">

                                <strong>
                                    Tanggapan Admin
                                </strong>

                                <p>
                                    {{ $keluhan->tanggapan }}
                                </p>

                            </div>

                        @endif

                    </div>

                @empty

                    <div class="empty">
                        Belum ada keluhan yang kamu kirim.
                    </div>

                @endforelse

            </div>


        {{-- =====================================================
             BAGIAN ADMIN
        ====================================================== --}}

        @elseif (Auth::user()->role === 'admin')

            <div class="card">

                <h2>Daftar Keluhan Penghuni</h2>

                @forelse ($keluhans as $keluhan)

                    <div class="complaint">

                        <div class="user-info">

                            <div class="user-name">
                                {{ $keluhan->user->name ?? 'Pengguna' }}
                            </div>

                            <div class="user-email">
                                {{ $keluhan->user->email ?? '-' }}
                            </div>

                        </div>


                        <div class="complaint-top">

                            <span class="date">
                                {{ $keluhan->created_at->format('d M Y, H:i') }}
                            </span>

                            <span class="status status-{{ $keluhan->status }}">

                                @if ($keluhan->status === 'menunggu')
                                    Menunggu
                                @elseif ($keluhan->status === 'diproses')
                                    Diproses
                                @else
                                    Selesai
                                @endif

                            </span>

                        </div>


                        <span class="complaint-label">
                            Keluhan
                        </span>

                        <div class="complaint-text">
                            {{ $keluhan->isi_keluhan }}
                        </div>


                        @if ($keluhan->foto)

                            <div class="complaint-photo">

                                <span class="complaint-label">
                                    Foto Keluhan
                                </span>

                                <img
                                    src="{{ asset('storage/' . $keluhan->foto) }}"
                                    alt="Foto keluhan">

                            </div>

                        @endif


                        <div class="admin-form">

                            <form
                                action="{{ route('admin.keluhan.update', $keluhan) }}"
                                method="POST">

                                @csrf
                                @method('PUT')


                                <div class="form-group">

                                    <label class="form-label">
                                        Status Keluhan
                                    </label>

                                    <select name="status" required>

                                        <option
                                            value="menunggu"
                                            {{ $keluhan->status === 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>

                                        <option
                                            value="diproses"
                                            {{ $keluhan->status === 'diproses' ? 'selected' : '' }}>
                                            Diproses
                                        </option>

                                        <option
                                            value="selesai"
                                            {{ $keluhan->status === 'selesai' ? 'selected' : '' }}>
                                            Selesai
                                        </option>

                                    </select>

                                </div>


                                <div class="form-group">

                                    <label class="form-label">
                                        Tanggapan Admin
                                    </label>

                                    <textarea
                                        name="tanggapan"
                                        placeholder="Tuliskan tanggapan untuk penghuni...">{{ old('tanggapan', $keluhan->tanggapan) }}</textarea>

                                </div>


                                <button type="submit" class="button">
                                    Simpan Perubahan
                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div class="empty">
                        Belum ada keluhan dari penghuni.
                    </div>

                @endforelse

            </div>

        @endif

    </main>

</body>

</html>
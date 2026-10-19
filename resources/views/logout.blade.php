<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Logout</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .logout-card {
            background: white;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            width: 320px;
            max-width: calc(100% - 70px);
        }

        .logout-card h2 {
            color: #1e293b;
            margin-bottom: 12px;
        }

        .logout-card p {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-logout {
            background: #dc2626;
            color: white;
        }
    </style>
</head>
<body>

    <div class="logout-card">
        <h2>Konfirmasi Logout</h2>

        <p>
            Apakah kamu yakin ingin keluar dari akun?
        </p>

        <div class="actions">
            <a href="{{ route('dashboard') }}" class="btn btn-cancel">
                Batal
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-logout">
                    Ya, Logout
                </button>
            </form>
        </div>
    </div>

</body>
</html>
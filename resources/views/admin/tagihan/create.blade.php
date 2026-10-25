<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tagihan | Kost Athia</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, sans-serif;
            color: #172554;
            background: #f1f5f9;
        }

        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 16px; }
        main { width: min(720px, 100%); margin: 0 auto; }
        .brand { color: #2563eb; font-size: 14px; font-weight: 700; margin-bottom: 20px; }
        .card { background: #fff; border: 1px solid #dbeafe; border-radius: 14px; padding: 24px; box-shadow: 0 8px 24px #0f172a0a; }
        h1 { margin: 0 0 8px; font-size: 24px; }
        p { color: #64748b; line-height: 1.6; }
        label { display: block; margin: 18px 0 7px; font-size: 14px; font-weight: 700; }
        input, select { width: 100%; min-height: 44px; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: white; color: #172554; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 16px; }
        .actions { display: flex; gap: 10px; margin-top: 24px; flex-wrap: wrap; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: white; font-weight: 700; text-decoration: none; cursor: pointer; }
        .button.secondary { background: #e2e8f0; color: #1e293b; }
        .alert { margin: 16px 0; padding: 12px 14px; border-radius: 8px; background: #fef2f2; color: #991b1b; }
        .alert ul { margin: 6px 0 0; padding-left: 20px; }
        .empty { padding: 16px; border-radius: 8px; background: #eff6ff; color: #1e3a8a; }
        @media (max-width: 560px) {
            body { padding: 20px 12px; }
            .card { padding: 18px; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    <main>
        <div class="brand">Kost Athia</div>

        <section class="card">
            <h1>Buat Tagihan Sewa</h1>
            <p>Pilih penghuni dan kamar dari booking yang memenuhi syarat. Status pembayaran awal penghuni harus disetujui.</p>

            @if ($errors->any())
                <div class="alert" role="alert">
                    <strong>Tagihan belum dapat disimpan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($bookings->isEmpty())
                <div class="empty">
                    Belum ada booking penghuni yang layak ditagih. Pastikan pembayaran awal sudah disetujui dan booking telah berstatus disetujui atau kamar ditandai terisi.
                </div>
            @else
                <form action="{{ route('admin.tagihan.store') }}" method="POST">
                    @csrf

                    <label for="user_id">Penghuni</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">Pilih penghuni</option>
                        @foreach ($residents as $resident)
                            <option value="{{ $resident->id }}" @selected((string) old('user_id') === (string) $resident->id)>
                                {{ $resident->name }} ({{ $resident->email }})
                            </option>
                        @endforeach
                    </select>

                    <label for="room_id">Kamar dari booking penghuni</label>
                    <select id="room_id" name="room_id" required disabled>
                        <option value="">Pilih penghuni terlebih dahulu</option>
                        @foreach ($bookings as $booking)
                            <option
                                value="{{ $booking->room_id }}"
                                data-user-id="{{ $booking->user_id }}"
                                @selected((string) old('room_id') === (string) $booking->room_id)>
                                {{ $booking->room?->kode_kamar ?? 'Kamar tidak tersedia' }} — {{ $booking->room?->nama ?? '—' }}
                            </option>
                        @endforeach
                    </select>

                    <label for="jenis_pembayaran">Jenis pembayaran</label>
                    <select id="jenis_pembayaran" name="jenis_pembayaran" required>
                        <option value="{{ $jenisPembayaran }}" @selected(old('jenis_pembayaran', $jenisPembayaran) === $jenisPembayaran)>
                            {{ $jenisPembayaran }}
                        </option>
                    </select>

                    <div class="grid">
                        <div>
                            <label for="periode">Periode</label>
                            <input id="periode" name="periode" type="month" value="{{ old('periode') }}" required>
                        </div>
                        <div>
                            <label for="nominal">Nominal (Rp)</label>
                            <input id="nominal" name="nominal" type="number" min="1" step="1" value="{{ old('nominal') }}" required>
                        </div>
                    </div>

                    <label for="jatuh_tempo">Tanggal jatuh tempo</label>
                    <input id="jatuh_tempo" name="jatuh_tempo" type="date" value="{{ old('jatuh_tempo') }}" required>

                    <div class="actions">
                        <button class="button" type="submit">Simpan Tagihan</button>
                        <a class="button secondary" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
                    </div>
                </form>
            @endif
        </section>
    </main>

    <script>
        const residentSelect = document.getElementById('user_id');
        const roomSelect = document.getElementById('room_id');
        const previousRoomId = @json((string) old('room_id', ''));

        if (residentSelect && roomSelect) {
            const filterRooms = () => {
                const residentId = residentSelect.value;
                let matchingRooms = 0;

                for (const option of roomSelect.options) {
                    if (!option.dataset.userId) {
                        option.hidden = false;
                        continue;
                    }

                    const matches = option.dataset.userId === residentId;
                    option.hidden = !matches;
                    option.disabled = !matches;
                    if (matches) matchingRooms++;
                }

                roomSelect.disabled = !residentId || matchingRooms === 0;
                if (!previousRoomId || !Array.from(roomSelect.options).some(
                    option => option.value === previousRoomId && !option.disabled
                )) {
                    roomSelect.value = '';
                }
            };

            residentSelect.addEventListener('change', filterRooms);
            filterRooms();
        }
    </script>
</body>

</html>

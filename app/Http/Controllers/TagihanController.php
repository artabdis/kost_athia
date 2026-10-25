<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class TagihanController extends Controller
{
    private const JENIS_SEWA_KAMAR = 'Sewa Kamar';

    /*Mengambil tagihan milik user yang sedang login.*/
    public function index()
    {
        $tagihans = Tagihan::with('room')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dashboard', compact('tagihans'));
    }

    /* Menampilkan detail tagihan*/
    public function show(Tagihan $tagihan): View
    {
        $user = Auth::user();
        abort_unless(
            $user->role === 'admin'
                || ($user->role === 'user' && $tagihan->user_id === $user->id),
            403
        );

        $tagihan->load(['room', 'user']);
        $statusPenghuni = $tagihan->user?->payment_status === 'approved'
            ? 'approved'
            : 'pending';

        return view('tagihan', compact('tagihan', 'statusPenghuni'));
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $bookings = Booking::query()
            ->eligibleForRent()
            ->with(['user:id,name,email', 'room:id,kode_kamar,nama'])
            ->orderBy('user_id')
            ->orderBy('room_id')
            ->get();

        return view('admin.tagihan.create', [
            'bookings' => $bookings,
            'residents' => $bookings
                ->pluck('user')
                ->unique('id')
                ->sortBy('name')
                ->values(),
            'jenisPembayaran' => self::JENIS_SEWA_KAMAR,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(
                    fn (QueryBuilder $query) => $query
                        ->where('role', 'user')
                        ->where('payment_status', 'approved')
                ),
            ],
            'room_id' => ['required', 'exists:rooms,id'],
            'jenis_pembayaran' => ['required', Rule::in([self::JENIS_SEWA_KAMAR])],
            'periode' => ['required', 'date_format:Y-m'],
            'nominal' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'jatuh_tempo' => ['required', 'date'],
        ]);

        $periode = Carbon::createFromFormat('Y-m', $validated['periode'])
            ->startOfMonth()
            ->locale('id')
            ->translatedFormat('F Y');

        DB::transaction(function () use ($validated, $periode): void {
            $resident = User::query()
                ->where('role', 'user')
                ->where('payment_status', 'approved')
                ->lockForUpdate()
                ->findOrFail($validated['user_id']);

            $booking = Booking::query()
                ->eligibleForRent()
                ->where('user_id', $resident->id)
                ->where('room_id', $validated['room_id'])
                ->exists();

            if (! $booking) {
                throw ValidationException::withMessages([
                    'room_id' => 'Pilih kamar dari booking penghuni yang memenuhi syarat.',
                ]);
            }

            $duplicate = Tagihan::query()
                ->where('user_id', $resident->id)
                ->where('jenis_pembayaran', $validated['jenis_pembayaran'])
                ->where('periode', $periode)
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'periode' => 'Tagihan untuk jenis pembayaran dan periode ini sudah tersedia.',
                ]);
            }

            $resident->tagihans()->create([
                'room_id' => $validated['room_id'],
                'jenis_pembayaran' => $validated['jenis_pembayaran'],
                'periode' => $periode,
                'nominal' => $validated['nominal'],
                'jatuh_tempo' => $validated['jatuh_tempo'],
                'status' => 'belum_bayar',
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tagihan sewa berhasil dibuat.');
    }

    /*Mengirim bukti pembayaran tagihan oleh penghuni.*/
    public function bayar(Request $request, Tagihan $tagihan): RedirectResponse
    {
        abort_unless(
            $request->user()->role === 'user'
                && $tagihan->user_id === $request->user()->id,
            403
        );

        if ($tagihan->status === 'lunas') {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Tagihan ini sudah lunas.');
        }

        abort_unless(in_array($tagihan->status, ['belum_bayar', 'ditolak'], true), 409);

        $validated = $request->validate([
            'bukti_pembayaran' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $path = $validated['bukti_pembayaran']
            ->store('tagihan-proofs', 'local');

        if ($path === false) {
            throw new RuntimeException('Bukti pembayaran gagal disimpan.');
        }

        try {
            DB::transaction(function () use ($tagihan, $path): void {
                $lockedTagihan = Tagihan::query()
                    ->lockForUpdate()
                    ->findOrFail($tagihan->id);

                abort_unless(in_array($lockedTagihan->status, ['belum_bayar', 'ditolak'], true), 409);

                $lockedTagihan->update([
                    'bukti_pembayaran' => $path,
                    'status' => 'menunggu_verifikasi',
                    'catatan' => null,
                    'tanggal_bayar' => null,
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('tagihan.show', $tagihan)
            ->with(
                'success',
                'Bukti pembayaran tagihan berhasil dikirim dan menunggu verifikasi admin.'
            );
    }

    /**
     * Admin melihat bukti pembayaran tagihan.
     */
    public function proof(Request $request, Tagihan $tagihan): StreamedResponse
    {
        $this->authorizeAdmin($request);
        abort_unless($tagihan->bukti_pembayaran, 404);

        return Storage::disk('local')->response(
            $tagihan->bukti_pembayaran,
            null,
            [
                'Cache-Control' => 'private, no-store',
            ],
            'inline'
        );
    }

    /* Admin melakukan verifikasi tagihan.*/
    public function verify(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $this->authorizeAdmin($request);

        abort_unless(
            $tagihan->status === 'menunggu_verifikasi'
                && $tagihan->bukti_pembayaran,
            404
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:lunas,ditolak',
            ],
            'catatan' => [
                'nullable',
                'required_if:status,ditolak',
                'string',
                'max:1000',
            ],
        ], [
            'catatan.required_if' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($tagihan, $validated): void {
            $lockedTagihan = Tagihan::query()
                ->lockForUpdate()
                ->findOrFail($tagihan->id);

            abort_unless(
                $lockedTagihan->status === 'menunggu_verifikasi'
                    && $lockedTagihan->bukti_pembayaran,
                409
            );

            $lockedTagihan->update([
                'status' => $validated['status'],
                'catatan' => $validated['status'] === 'ditolak'
                    ? $validated['catatan']
                    : null,
                'tanggal_bayar' => $validated['status'] === 'lunas' ? now() : null,
            ]);

            if ($validated['status'] === 'lunas') {
                $resident = User::query()
                    ->where('role', 'user')
                    ->lockForUpdate()
                    ->findOrFail($lockedTagihan->user_id);

                $resident->forceFill(['payment_status' => 'approved'])->save();

                Booking::query()
                    ->where('user_id', $resident->id)
                    ->where('room_id', $lockedTagihan->room_id)
                    ->whereIn('status', ['pending', 'menunggu_verifikasi', 'approved'])
                    ->lockForUpdate()
                    ->get()
                    ->each(fn (Booking $booking) => $booking->update(['status' => 'approved']));
            }
        });

        if ($validated['status'] === 'lunas') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'success',
                    'Pembayaran tagihan berhasil diverifikasi. Status tagihan sekarang Lunas.'
                );
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Pembayaran tagihan ditolak. Penghuni dapat mengunggah bukti pembayaran kembali.'
            );
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}

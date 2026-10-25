<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tagihan;
use App\Models\User;
use App\Services\RoomBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user()->refresh();
        $bookingRoomId = $request->session()->pull('booking_room_id');

        if ($user->role === 'user' && $bookingRoomId) {
            try {
                app(RoomBookingService::class)->reserve($user, (int) $bookingRoomId);
            } catch (ValidationException $exception) {
                return redirect()
                    ->route('kamar')
                    ->with('error', $exception->errors()['booking'][0] ?? 'Kamar sudah tidak tersedia.');
            }
        }

        if ($user->role === 'admin' || $user->isApprovedResident()) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Login berhasil.');
        }

        return redirect()
            ->route('payment.show')
            ->with(
                'info',
                'Silakan selesaikan pembayaran. Dashboard dapat diakses setelah pembayaran diverifikasi admin.'
            );
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
                'regex:/^[A-Za-z0-9]+$/',
            ],
        ], [
            'password.regex' => 'Password hanya boleh mengandung huruf dan angka.',
            'email.email' => 'Isi data dengan valid.',
        ]);

        /* Ambil kamar yang dipilih sebelum session diregenerasi*/
        $bookingRoomId = $request->session()->get('booking_room_id');

        /* Buat akun*/
        $user = DB::transaction(function () use ($validated, $bookingRoomId): User {
            $role = User::query()->doesntExist() ? 'admin' : 'user';

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->forceFill([
                'role' => $role,
            ])->save();

            if ($role === 'user' && $bookingRoomId) {
                app(RoomBookingService::class)->reserve($user, (int) $bookingRoomId);
            }

            return $user;
        });

        $request->session()->forget('booking_room_id');

        /* Login otomatis setelah registrasi*/
        Auth::login($user);

        $request->session()->regenerate();

        /*Redirect sesuai role*/
        if ($user->role === 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Registrasi berhasil.');
        }

        if ($bookingRoomId) {
            $tagihan = $user->tagihans()->latest()->firstOrFail();

            return redirect()
                ->route('tagihan.show', $tagihan)
                ->with('success', 'Registrasi berhasil. Silakan unggah bukti pembayaran tagihan kamar.');
        }

        return redirect()
            ->route('payment.show')
            ->with(
                'success',
                'Registrasi berhasil. Silakan unggah bukti pembayaran untuk diverifikasi admin.'
            );
    }

    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user()->refresh();

        /*DASHBOARD ADMIN*/
        if ($user->role === 'admin') {
            return view('dashboard', [
                'payments' => User::query()
                    ->where('role', 'user')
                    ->whereDoesntHave('tagihans')
                    ->latest('updated_at')
                    ->take(10)
                    ->get(),

                'residents' => User::query()
                    ->where('role', 'user')
                    ->latest()
                    ->take(10)
                    ->get(),

                'rooms' => Room::query()
                    ->orderBy('kode_kamar')
                    ->get(),

                'tagihans' => Tagihan::query()
                    ->with(['user', 'room'])
                    ->latest()
                    ->get(),

                'stats' => [
                    'rooms' => Room::query()->count(),

                    'availableRooms' => Room::query()
                        ->where('status', 'tersedia')
                        ->count(),

                    'residents' => User::query()
                        ->where('role', 'user')
                        ->where('payment_status', 'approved')
                        ->count(),

                    'pendingPayments' => Tagihan::query()
                        ->where('status', 'menunggu_verifikasi')
                        ->count(),
                ],
            ]);
        }

        /*DASHBOARD PENGHUNI*/
        abort_unless($user->role === 'user', 403);

        /* PEMBAYARAN AWAL TETAP SEPERTI SEBELUMNYA*/
        if (! $user->isApprovedResident()) {
            return redirect()
                ->route('payment.show')
                ->with(
                    'info',
                    'Dashboard dapat diakses setelah admin memverifikasi pembayaran Anda.'
                );
        }

        $tagihans = Tagihan::query()
            ->with('room')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard', [
            /*Booking hanya milik user yang sedang login.*/
            'bookings' => $user->bookings()
                ->with('room')
                ->latest()
                ->get(),

            /* Tagihan hanya milik user yang sedang login.*/
            'tagihans' => $tagihans,
            'tagihanAktif' => $tagihans->first(
                fn (Tagihan $tagihan): bool => $tagihan->status !== 'lunas'
            ) ?? $tagihans->first(),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

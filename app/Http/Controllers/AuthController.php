<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use App\Models\Tagihan;

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

        $user = Auth::user();

        if ($user->role === 'admin' || $user->payment_status === 'approved') {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Login berhasil.');
        }

        return redirect()
            ->route('payment.show')
            ->with('info', 'Silakan selesaikan pembayaran. Dashboard dapat diakses setelah pembayaran diverifikasi admin.');
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

        // Buat akun dan pertahankan aturan role yang sudah ada.
        $user = DB::transaction(function () use ($validated): User {
            $role = User::query()->doesntExist() ? 'admin' : 'user';

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->forceFill(['role' => $role])->save();

            return $user;
        });

        // Login otomatis setelah registrasi berhasil.
        Auth::login($user);

        // Perbarui session tanpa menghapus pilihan kamar.
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Registrasi berhasil.');
        }

        return redirect()
            ->route('payment.show')
            ->with('success', 'Registrasi berhasil. Silakan unggah bukti pembayaran untuk diverifikasi admin.');
    }

    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return view('dashboard', [
                'payments' => User::query()
                    ->where('role', 'user')
                    ->whereNotNull('payment_proof')
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

                'bookings' => Booking::query()
                    ->with([
                        'user:id,name,email',
                        'room:id,kode_kamar,nama,harga,status',
                    ])
                    ->latest()
                    ->take(10)
                    ->get(),

                'tagihans' => Tagihan::query()
                    ->with(['user', 'room'])
                    ->latest()
                    ->take(10)
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

                    'pendingPayments' => User::query()
                        ->where('role', 'user')
                        ->where('payment_status', 'submitted')
                        ->count(),
                ],
            ]);
        }

        abort_unless($user->role === 'user', 403);

        // PEMBAYARAN AWAL TETAP SEPERTI SEBELUMNYA
        if ($user->payment_status !== 'approved') {
            return redirect()
                ->route('payment.show')
                ->with(
                    'info',
                    'Dashboard dapat diakses setelah admin memverifikasi pembayaran Anda.'
                );
        }

        return view('dashboard', [
            'bookings' => $user->bookings()
                ->with('room')
                ->latest()
                ->get(),

            // TAGIHAN HANYA MILIK USER YANG SEDANG LOGIN
            'tagihans' => Tagihan::query()
                ->with('room')
                ->where('user_id', $user->id)
                ->latest()
                ->get(),
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

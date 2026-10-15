<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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
                    'email' => 'Email atau password salah.'
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            $role = User::query()->doesntExist() ? 'admin' : 'user';

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->forceFill(['role' => $role])->save();
        });

        return redirect()
            ->route('login')
            ->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return view('dashboard', [
                'payments' => User::query()
                    ->where('role', 'user')
                    ->where('payment_status', 'submitted')
                    ->latest()
                    ->get(),
            ]);
        }

        abort_unless($user->role === 'user', 403);

        if ($user->payment_status !== 'approved') {
            return redirect()
                ->route('payment.show')
                ->with('info', 'Dashboard dapat diakses setelah admin memverifikasi pembayaran Anda.');
        }

        return view('dashboard', ['buku' => collect()]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

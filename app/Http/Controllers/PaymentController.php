<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::user()->role === 'admin' || Auth::user()->payment_status === 'approved') {
            return redirect()->route('dashboard');
        }

        return view('payment');
    }

    public function upload(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->role === 'user', 403);

        if ($user->payment_status === 'approved') {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $validated['payment_proof']->store('payment-proofs', 'local');

        if ($path === false) {
            throw new RuntimeException('Bukti pembayaran gagal disimpan.');
        }

        if ($user->payment_proof) {
            Storage::disk('local')->delete($user->payment_proof);
        }

        $user->forceFill([
            'payment_proof' => $path,
            'payment_status' => 'submitted',
            'payment_note' => null,
        ])->save();

        return redirect()
            ->route('payment.show')
            ->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function verify(Request $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);
        abort_unless(
            $user->role === 'user'
                && $user->payment_status === 'submitted'
                && $user->payment_proof,
            404
        );

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ]);

        $user->forceFill([
            'payment_status' => $validated['status'],
            'payment_note' => $validated['status'] === 'rejected'
                ? ($validated['note'] ?? null)
                : null,
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('success', $validated['status'] === 'approved'
                ? 'Pembayaran pengguna berhasil diverifikasi.'
                : 'Bukti pembayaran ditolak.');
    }

    public function proof(User $user): StreamedResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);
        abort_unless($user->role === 'user' && $user->payment_proof, 404);

        return Storage::disk('local')->response(
            $user->payment_proof,
            null,
            ['Cache-Control' => 'private, no-store'],
            'inline'
        );
    }
}

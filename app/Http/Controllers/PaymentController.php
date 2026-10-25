<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = Auth::user()->refresh();

        if ($user->role === 'admin' || $user->isApprovedResident()) {
            return redirect()->route('dashboard');
        }

        $tagihan = $user->tagihans()
            ->where('status', '!=', 'lunas')
            ->latest()
            ->first()
            ?? $user->tagihans()->latest()->first();

        if ($tagihan) {
            return redirect()->route('tagihan.show', $tagihan);
        }

        return view('payment');
    }

    public function upload(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->role === 'user', 403);

        $user->refresh();
        if ($user->isApprovedResident()) {
            return redirect()->route('dashboard');
        }

        $tagihan = $user->tagihans()
            ->where('status', '!=', 'lunas')
            ->latest()
            ->first()
            ?? $user->tagihans()->latest()->first();

        if ($tagihan) {
            return redirect()
                ->route('tagihan.show', $tagihan)
                ->with('info', 'Unggah bukti pembayaran melalui detail tagihan Anda.');
        }

        $validated = $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $validated['payment_proof']->store('payment-proofs', 'local');

        if ($path === false) {
            throw new RuntimeException('Bukti pembayaran gagal disimpan.');
        }

        $previousProof = $user->payment_proof;

        $user->forceFill([
            'payment_proof' => $path,
            'payment_status' => 'submitted',
            'payment_note' => null,
        ])->save();

        if ($previousProof) {
            Storage::disk('local')->delete($previousProof);
        }

        return redirect()
            ->route('payment.show')
            ->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function verify(Request $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($user, $validated): void {
            $lockedUser = User::query()
                ->where('role', 'user')
                ->lockForUpdate()
                ->findOrFail($user->id);

            abort_if($lockedUser->tagihans()->exists(), 409);

            abort_unless(
                $lockedUser->payment_status === 'submitted'
                    && $lockedUser->payment_proof,
                404
            );

            $lockedUser->forceFill([
                'payment_status' => $validated['status'],
                'payment_note' => $validated['status'] === 'rejected'
                    ? ($validated['note'] ?? null)
                    : null,
            ])->save();
        });

        return redirect()
            ->route('dashboard')
            ->with('success', $validated['status'] === 'approved'
                ? 'Pembayaran pengguna berhasil diverifikasi.'
                : 'Bukti pembayaran ditolak.');
    }

    public function proof(User $user): StreamedResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);
        $user->refresh();
        abort_unless($user->role === 'user' && $user->payment_proof, 404);

        return Storage::disk('local')->response(
            $user->payment_proof,
            null,
            ['Cache-Control' => 'private, no-store'],
            'inline'
        );
    }
}

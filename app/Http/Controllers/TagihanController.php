<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TagihanController extends Controller
{
    /**
     * Mengambil tagihan milik user yang sedang login.
     */
    public function index()
    {
        $tagihans = Tagihan::with('room')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dashboard', compact('tagihans'));
    }

    /**
     * Menampilkan detail tagihan.
     */
    public function show(Tagihan $tagihan)
    {
        abort_unless($tagihan->user_id === Auth::id(), 403);

        $tagihan->load('room');

        return view('tagihan', compact('tagihan'));
    }

    /**
     * Mengirim bukti pembayaran tagihan oleh penghuni.
     */
    public function bayar(Request $request, Tagihan $tagihan): RedirectResponse
    {
        abort_unless($tagihan->user_id === Auth::id(), 403);

        if ($tagihan->status === 'lunas') {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Tagihan ini sudah lunas.');
        }

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

        $tagihan->update([
            'bukti_pembayaran' => $path,
            'status' => 'menunggu_verifikasi',
            'catatan' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Bukti pembayaran tagihan berhasil dikirim dan menunggu verifikasi admin.'
            );
    }

    /**
     * Admin melihat bukti pembayaran tagihan.
     */
    public function proof(Tagihan $tagihan): StreamedResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        abort_unless(
            $tagihan->status === 'menunggu_verifikasi'
                && $tagihan->bukti_pembayaran,
            404
        );

        return Storage::disk('local')->response(
            $tagihan->bukti_pembayaran,
            null,
            [
                'Cache-Control' => 'private, no-store',
            ],
            'inline'
        );
    }

    /**
     * Admin melakukan verifikasi tagihan.
     */
    public function verify(Request $request, Tagihan $tagihan): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);

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

        if ($validated['status'] === 'lunas') {

            $tagihan->update([
                'status' => 'lunas',
                'catatan' => null,
                'tanggal_bayar' => now(),
            ]);

            return redirect()
                ->route('dashboard')
                ->with(
                    'success',
                    'Pembayaran tagihan berhasil diverifikasi. Status tagihan sekarang Lunas.'
                );
        }

        $tagihan->update([
            'status' => 'ditolak',
            'catatan' => $validated['catatan'],
            'tanggal_bayar' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Pembayaran tagihan ditolak. Penghuni dapat mengunggah bukti pembayaran kembali.'
            );
    }
}
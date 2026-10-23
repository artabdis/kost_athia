<?php

namespace App\Http\Controllers;

use App\Models\Keluhan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeluhanController extends Controller
{
    // =========================
    // PENGHUNI - LIHAT KELUHAN
    // =========================
    public function index()
    {
        abort_unless(Auth::user()->role === 'user', 403);

        $keluhans = Keluhan::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('keluhan', compact('keluhans'));
    }

    // =========================
    // PENGHUNI - KIRIM KELUHAN
    // =========================
    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'user', 403);

        $validated = $request->validate([
            'isi_keluhan' => [
                'required',
                'string',
                'max:2000',
            ],

            'foto' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'isi_keluhan.required' => 'Keluhan wajib diisi.',
            'isi_keluhan.max' => 'Keluhan maksimal 2000 karakter.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('keluhan-fotos', 'public');
        }

        Keluhan::create([
            'user_id' => Auth::id(),
            'isi_keluhan' => $validated['isi_keluhan'],
            'foto' => $fotoPath,
            'status' => 'menunggu',
        ]);

        return redirect()
            ->route('keluhan.index')
            ->with('success', 'Keluhan berhasil dikirim.');
    }

    // =========================
    // ADMIN - LIHAT SEMUA KELUHAN
    // =========================
    public function adminIndex()
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $keluhans = Keluhan::query()
            ->with('user')
            ->latest()
            ->get();

        return view('keluhan', compact('keluhans'));
    }

    // =========================
    // ADMIN - UPDATE KELUHAN
    // =========================
    public function update(Request $request, Keluhan $keluhan): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:menunggu,diproses,selesai',
            ],

            'tanggapan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $keluhan->update([
            'status' => $validated['status'],
            'tanggapan' => $validated['tanggapan'] ?? null,
        ]);

        return redirect()
            ->route('admin.keluhan.index')
            ->with('success', 'Keluhan berhasil diperbarui.');
    }
}
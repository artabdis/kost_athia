<?php

namespace App\Http\Controllers;

use App\Models\Room;

class RoomsController extends Controller
{
    /**
     * Menampilkan daftar kamar.
     */
    public function index()
    {
        $kamar = Room::all();

        return view('kamar', compact('kamar'));
    }

    /**
     * Menampilkan detail kamar melalui kamar.blade.php.
     */
    public function show($id)
    {
        $detailKamar = Room::findOrFail($id);
        $kamar = Room::all();

        return view('kamar', compact('kamar', 'detailKamar'));
    }

    /**
     * Menampilkan halaman detail VIP.
     */
    public function showVip()
    {
        return view('detail-vip');
    }

    /**
     * Booking kamar.
     */
    public function booking($id)
    {
        $kamar = Room::findOrFail($id);

        if ($kamar->status !== 'tersedia') {
            return redirect()
                ->route('kamar')
                ->with('error', 'Kamar ini sudah terisi.');
        }

        session(['booking_room_id' => $kamar->id]);

        if (!auth()->check()) {
            return redirect()
                ->route('register')
                ->with('success', 'Silakan registrasi untuk melanjutkan booking.');
        }

        return redirect()->route('payment.show');
    }

    /**
     * Booking kamar VIP.
     */
    public function bookingVip()
    {
        $kamar = Room::where('kode_kamar', 'A01')->firstOrFail();

        if ($kamar->status !== 'tersedia') {
            return redirect()
                ->route('kamar')
                ->with('error', 'Kamar VIP sedang tidak tersedia.');
        }

        session(['booking_room_id' => $kamar->id]);

        if (!auth()->check()) {
            return redirect()
                ->route('register')
                ->with('success', 'Silakan registrasi untuk melanjutkan booking.');
        }

        return redirect()->route('payment.show');
    }
}

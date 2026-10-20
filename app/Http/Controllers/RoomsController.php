<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\View\View;

class RoomsController extends Controller
{
    private const LEGACY_ROOM_DETAILS = [
        'A01' => [
            'nama' => 'Kamar A01',
            'lokasi' => 'Lantai 1',
            'harga' => 750000,
            'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=80',
        ],
        'B01' => [
            'nama' => 'Kamar B01',
            'lokasi' => 'Lantai 2',
            'harga' => 1000000,
            'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=80',
        ],
        'B02' => [
            'nama' => 'Kamar B02',
            'lokasi' => 'Lantai 2',
            'harga' => 1000000,
            'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=80',
        ],
    ];

    /**
     * Menampilkan daftar kamar.
     */
    public function index()
    {
        $kamar = Room::query()->orderBy('kode_kamar')->get();

        return view('kamar', compact('kamar'));
    }

    /**
     * Menampilkan detail kamar berdasarkan ID atau kode kamar.
     */
    public function show(string $id): View
    {
        $room = ctype_digit($id)
            ? Room::query()->find($id)
            : Room::query()->where('kode_kamar', $id)->first();

        if ($room) {
            return view('detail-kamar', [
                'kamar' => $room,
                'image' => self::LEGACY_ROOM_DETAILS[$room->kode_kamar]['image'] ?? null,
                'bookingEnabled' => $room->exists && $room->status === 'tersedia',
            ]);
        }

        $code = strtoupper($id);
        abort_unless(isset(self::LEGACY_ROOM_DETAILS[$code]), 404);

        $details = self::LEGACY_ROOM_DETAILS[$code];
        $kamar = new Room([
            'kode_kamar' => $code,
            'nama' => $details['nama'],
            'lokasi' => $details['lokasi'],
            'harga' => $details['harga'],
            'status' => null,
        ]);

        return view('detail-kamar', [
            'kamar' => $kamar,
            'image' => $details['image'],
            'bookingEnabled' => false,
        ]);
    }

    /**
     * Menampilkan halaman detail VIP.
     */
    public function showVip(): View
    {
        return view('detail-vip', [
            'room' => Room::query()->where('kode_kamar', 'VIP')->first(),
        ]);
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

        if (! auth()->check()) {
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
        $kamar = Room::query()->where('kode_kamar', 'VIP')->first();

        if (! $kamar) {
            return redirect()
                ->route('kamar')
                ->with('error', 'Data kamar VIP belum tersedia.');
        }

        if ($kamar->status !== 'tersedia') {
            return redirect()
                ->route('kamar')
                ->with('error', 'Kamar VIP sedang tidak tersedia.');
        }

        session(['booking_room_id' => $kamar->id]);

        if (! auth()->check()) {
            return redirect()
                ->route('register')
                ->with('success', 'Silakan registrasi untuk melanjutkan booking.');
        }

        return redirect()->route('payment.show');
    }
}

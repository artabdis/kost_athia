<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoomsController extends Controller
{
    /**
     * Daftar gambar berdasarkan kode kamar.
     */
    private function images(): array
    {
        return [
            'A01' => asset('images/km1.jpg'),
            'B01' => asset('images/tipe b.jpg'),
            'B02' => asset('images/km2 dan 3.jpg'),
            'VIP' => asset('images/vip.jpg'),
        ];
    }

    /**
     * Menampilkan daftar kamar.
     */
    public function index()
    {
        $kamar = Room::query()
            ->orderBy('kode_kamar')
            ->get();

        $images = $this->images();

        return view('kamar', compact('kamar', 'images'));
    }

    /**
     * Menampilkan detail kamar berdasarkan ID atau kode kamar.
     */
    public function show(string $id): View
    {
        $room = ctype_digit($id)
            ? Room::query()->find($id)
            : Room::query()->where('kode_kamar', strtoupper($id))->first();

        if (!$room) {
            abort(404);
        }

        $images = $this->images();

        return view('detail-kamar', [
            'kamar' => $room,
            'image' => $images[$room->kode_kamar] ?? null,
            'bookingEnabled' => $room->status === 'tersedia',
        ]);
    }

    /**
     * Menampilkan halaman detail VIP.
     */
    public function showVip(): View
    {
        $room = Room::query()
            ->where('kode_kamar', 'VIP')
            ->first();

        $images = $this->images();

        return view('detail-vip', [
            'room' => $room,
            'image' => $images['VIP'],
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

    if (!auth()->check()) {
        return redirect()
            ->route('register')
            ->with('success', 'Silakan registrasi untuk melanjutkan booking.');
    }

    // Jika user sudah login, simpan booking sekarang.
    $this->simpanBooking(auth()->user(), $kamar);

    return redirect()->route('payment.show');
}

public function bookingVip()
{
    $kamar = Room::query()
        ->where('kode_kamar', 'VIP')
        ->firstOrFail();

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

    $this->simpanBooking(auth()->user(), $kamar);

    return redirect()->route('payment.show');
}

private function simpanBooking(User $user, Room $room): Booking
{
    return DB::transaction(function () use ($user, $room) {
        $room = Room::query()
            ->lockForUpdate()
            ->findOrFail($room->id);

        if ($room->status !== 'tersedia') {
            abort(409, 'Kamar sudah tidak tersedia.');
        }

        // Jangan membuat booking ganda untuk kamar dan user yang sama.
        $booking = Booking::query()
            ->where('user_id', $user->id)
            ->where('room_id', $room->id)
            ->whereIn('status', ['pending', 'menunggu_verifikasi', 'approved'])
            ->first();

        if ($booking) {
            return $booking;
        }

        return Booking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);
    });
}
}
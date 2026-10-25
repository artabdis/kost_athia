<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\RoomBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoomsController extends Controller
{
    /** Daftar gambar berdasarkan kode kamar.*/
    private function images(): array
    {
        return [
            'A01' => asset('images/km1.jpg'),
            'B01' => asset('images/tipe b.jpg'),
            'B02' => asset('images/km2 dan 3.jpg'),
            'VIP' => asset('images/vip.jpg'),
        ];
    }

    /*Menampilkan daftar kamar.*/
    public function index()
    {
        $kamar = Room::query()
            ->withExists([
                'bookings as has_active_booking' => fn ($query) => $query->active(),
            ])
            ->orderBy('kode_kamar')
            ->get();

        $images = $this->images();

        return view('kamar', compact('kamar', 'images'));
    }

    /* Menampilkan detail kamar berdasarkan ID atau kode kamar*/
    public function show(string $id): View
    {
        $room = ctype_digit($id)
            ? Room::query()->find($id)
            : Room::query()->where('kode_kamar', strtoupper($id))->first();

        if (! $room) {
            abort(404);
        }

        $images = $this->images();

        return view('detail-kamar', [
            'kamar' => $room,
            'image' => $images[$room->kode_kamar] ?? null,
            'bookingEnabled' => app(RoomBookingService::class)->isAvailable($room),
        ]);
    }

    /* Menampilkan halaman detail VIP.*/
    public function showVip(): View
    {
        $room = Room::query()
            ->where('kode_kamar', 'VIP')
            ->first();

        $images = $this->images();

        return view('detail-vip', [
            'room' => $room,
            'image' => $images['VIP'],
            'bookingEnabled' => $room && app(RoomBookingService::class)->isAvailable($room),
        ]);
    }

    public function booking(Request $request, string $id, RoomBookingService $bookingService): RedirectResponse
    {
        $room = Room::query()->findOrFail($id);

        if (! $request->user()) {
            if (! $bookingService->isAvailable($room)) {
                return redirect()
                    ->route('kamar')
                    ->with('error', 'Kamar ini sudah dipesan atau tidak tersedia.');
            }

            $request->session()->put('booking_room_id', $room->id);

            return redirect()
                ->route('register')
                ->with('success', 'Silakan registrasi untuk melanjutkan booking.');
        }

        abort_unless($request->user()->role === 'user', 403);

        try {
            $bookingService->reserve($request->user(), $room->id);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('kamar')
                ->with('error', $exception->errors()['booking'][0] ?? 'Kamar sudah tidak tersedia.');
        }

        return redirect()
            ->route('payment.show')
            ->with('success', 'Booking kamar berhasil disimpan.');
    }

    public function bookingVip(Request $request, RoomBookingService $bookingService): RedirectResponse
    {
        $room = Room::query()
            ->where('kode_kamar', 'VIP')
            ->firstOrFail();

        if (! $request->user()) {
            if (! $bookingService->isAvailable($room)) {
                return redirect()
                    ->route('kamar')
                    ->with('error', 'Kamar VIP sudah dipesan atau tidak tersedia.');
            }

            $request->session()->put('booking_room_id', $room->id);

            return redirect()
                ->route('register')
                ->with('success', 'Silakan registrasi untuk melanjutkan booking.');
        }

        abort_unless($request->user()->role === 'user', 403);

        try {
            $bookingService->reserve($request->user(), $room->id);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('kamar')
                ->with('error', $exception->errors()['booking'][0] ?? 'Kamar VIP sudah tidak tersedia.');
        }

        return redirect()
            ->route('payment.show')
            ->with('success', 'Booking kamar VIP berhasil disimpan.');
    }
}

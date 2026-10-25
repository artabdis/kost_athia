<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Validation\ValidationException;

class RoomBookingService
{
    public function reserve(User $user, int $roomId): Booking
    {
        return DB::transaction(function () use ($user, $roomId): Booking {
            $lockedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            $room = Room::query()
                ->lockForUpdate()
                ->findOrFail($roomId);

            $existingBooking = Booking::query()
                ->where('user_id', $lockedUser->id)
                ->where('room_id', $room->id)
                ->active()
                ->first();

            if ($existingBooking) {
                $this->ensureInitialBill($lockedUser, $room);

                return $existingBooking;
            }

            if ($room->status !== 'tersedia' || $room->bookings()->active()->exists()) {
                throw ValidationException::withMessages([
                    'booking' => 'Kamar sudah dipesan atau tidak tersedia.',
                ]);
            }

            $booking = Booking::create([
                'user_id' => $lockedUser->id,
                'room_id' => $room->id,
                'status' => 'pending',
            ]);

            $this->ensureInitialBill($lockedUser, $room);

            return $booking;
        });
    }

    public function isAvailable(Room $room): bool
    {
        return $room->status === 'tersedia'
            && ! $room->bookings()->active()->exists();
    }

    private function ensureInitialBill(User $user, Room $room): void
    {
        $dueDay = (int) config('tagihan.tanggal_jatuh_tempo', 10);

        if ($dueDay < 1 || $dueDay > 28) {
            throw new RuntimeException(
                'Konfigurasi tagihan.tanggal_jatuh_tempo harus antara 1 dan 28.'
            );
        }

        $month = Carbon::now()->startOfMonth();
        $period = $month->copy()->locale('id')->translatedFormat('F Y');

        if (Tagihan::query()
            ->where('user_id', $user->id)
            ->where('room_id', $room->id)
            ->where('jenis_pembayaran', 'Sewa Kamar')
            ->where('periode', $period)
            ->exists()) {
            return;
        }

        $user->tagihans()->create([
            'room_id' => $room->id,
            'jenis_pembayaran' => 'Sewa Kamar',
            'periode' => $period,
            'nominal' => $room->harga,
            'jatuh_tempo' => $month->copy()->day($dueDay)->toDateString(),
            'status' => 'belum_bayar',
        ]);
    }
}

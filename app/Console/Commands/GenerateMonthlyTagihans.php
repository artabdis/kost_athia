<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyTagihans extends Command
{
    protected $signature = 'tagihan:generate-bulanan';

    protected $description = 'Membuat tagihan sewa bulanan untuk penghuni yang memenuhi syarat';

    public function handle(): int
    {
        $dueDay = (int) config('tagihan.tanggal_jatuh_tempo', 10);

        if ($dueDay < 1 || $dueDay > 28) {
            $this->error('Konfigurasi tagihan.tanggal_jatuh_tempo harus antara 1 dan 28.');

            return self::FAILURE;
        }

        $month = Carbon::now()->startOfMonth();
        $periode = $month->copy()->locale('id')->translatedFormat('F Y');
        $jatuhTempo = $month->copy()->day($dueDay)->toDateString();
        $eligibleBookings = Booking::query()
            ->eligibleForRent()
            ->with('room:id,harga')
            ->orderBy('user_id')
            ->orderBy('id')
            ->get()
            ->groupBy('user_id');

        $created = 0;
        $skipped = 0;

        foreach ($eligibleBookings as $userId => $bookings) {
            if ($bookings->count() !== 1) {
                $this->warn("Penghuni #{$userId} memiliki lebih dari satu booking yang layak; tagihan dilewati.");
                $skipped++;

                continue;
            }

            $booking = $bookings->first();

            $wasCreated = DB::transaction(function () use ($booking, $periode, $jatuhTempo, $userId): bool {
                $resident = User::query()
                    ->where('role', 'user')
                    ->where('payment_status', 'approved')
                    ->lockForUpdate()
                    ->find($userId);

                if (! $resident || ! Booking::query()
                    ->eligibleForRent()
                    ->whereKey($booking->id)
                    ->exists()) {
                    return false;
                }

                $alreadyExists = Tagihan::query()
                    ->where('user_id', $resident->id)
                    ->where('jenis_pembayaran', 'Sewa Kamar')
                    ->where('periode', $periode)
                    ->exists();

                if ($alreadyExists) {
                    return false;
                }

                $resident->tagihans()->create([
                    'room_id' => $booking->room_id,
                    'jenis_pembayaran' => 'Sewa Kamar',
                    'periode' => $periode,
                    'nominal' => $booking->room?->harga,
                    'jatuh_tempo' => $jatuhTempo,
                    'status' => 'belum_bayar',
                ]);

                return true;
            });

            if ($wasCreated) {
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->info("Tagihan {$periode}: {$created} dibuat, {$skipped} dilewati.");

        return self::SUCCESS;
    }
}

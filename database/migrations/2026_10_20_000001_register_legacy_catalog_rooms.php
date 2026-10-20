<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const ROOMS = [
        [
            'kode_kamar' => 'A01',
            'nama' => 'Kamar A01',
            'lokasi' => 'Lantai 1',
            'harga' => 750000,
            'status' => 'belum_dikonfirmasi',
        ],
        [
            'kode_kamar' => 'B01',
            'nama' => 'Kamar B01',
            'lokasi' => 'Lantai 2',
            'harga' => 1000000,
            'status' => 'belum_dikonfirmasi',
        ],
        [
            'kode_kamar' => 'B02',
            'nama' => 'Kamar B02',
            'lokasi' => 'Lantai 2',
            'harga' => 1000000,
            'status' => 'belum_dikonfirmasi',
        ],
    ];

    public function up(): void
    {
        foreach (self::ROOMS as $room) {
            if (DB::table('rooms')->where('kode_kamar', $room['kode_kamar'])->exists()) {
                continue;
            }

            DB::table('rooms')->insert([
                ...$room,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Keep catalog records on rollback to avoid removing edited or referenced rooms.
    }
};

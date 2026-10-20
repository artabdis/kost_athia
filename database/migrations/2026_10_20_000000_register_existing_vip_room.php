<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const VIP_ROOM = [
        'kode_kamar' => 'VIP',
        'nama' => 'Kamar VIP',
        'lokasi' => 'Lantai Utama (Akses Eksklusif)',
        'harga' => 1500000,
        'status' => 'tersedia',
    ];

    public function up(): void
    {
        if (! DB::table('rooms')->where('kode_kamar', self::VIP_ROOM['kode_kamar'])->exists()) {
            DB::table('rooms')->insert([
                ...self::VIP_ROOM,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Retain this data on rollback so existing edits and room history are not lost.
    }
};

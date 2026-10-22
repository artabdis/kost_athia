<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('room_id')
                ->constrained('rooms')
                ->cascadeOnDelete();

            $table->string('jenis_pembayaran')->default('Sewa Kamar');

            // Contoh: Oktober 2026
            $table->string('periode');

            $table->integer('nominal');

            $table->date('jatuh_tempo');

            // belum_bayar, menunggu_verifikasi, lunas, ditolak
            $table->string('status')->default('belum_bayar');

            $table->string('bukti_pembayaran')->nullable();

            $table->text('catatan')->nullable();

            $table->timestamp('tanggal_bayar')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
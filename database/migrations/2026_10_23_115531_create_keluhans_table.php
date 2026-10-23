<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('keluhans', function (Blueprint $table) {
            $table->id();

            // Pemilik keluhan
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Isi keluhan dari penghuni
            $table->text('isi_keluhan');

            // Status keluhan
            $table->enum('status', [
                'menunggu',
                'diproses',
                'selesai',
            ])->default('menunggu');

            // Tanggapan dari admin
            $table->text('tanggapan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluhans');
    }
};
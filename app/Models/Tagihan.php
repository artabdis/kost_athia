<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'jenis_pembayaran',
        'periode',
        'nominal',
        'jatuh_tempo',
        'status',
        'bukti_pembayaran',
        'catatan',
        'tanggal_bayar',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'tanggal_bayar' => 'datetime',
        'nominal' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keluhan extends Model
{
    protected $fillable = [
        'user_id',
        'isi_keluhan',
        'foto',
        'status',
        'tanggapan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
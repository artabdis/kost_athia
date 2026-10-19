<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'kode_kamar',
        'nama',
        'lokasi',
        'harga',
        'status',
    ];
}
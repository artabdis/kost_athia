<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    private const RENT_BOOKING_STATUSES = ['pending', 'menunggu_verifikasi', 'approved'];

    private const ACTIVE_STATUSES = ['pending', 'menunggu_verifikasi', 'approved'];

    protected $fillable = [
        'user_id',
        'room_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeEligibleForRent(Builder $query): Builder
    {
        return $query
            ->whereIn('bookings.status', self::RENT_BOOKING_STATUSES)
            ->where(function (Builder $query): void {
                $query
                    ->where('bookings.status', 'approved')
                    ->orWhereHas('room', fn (Builder $roomQuery) => $roomQuery->where('status', 'terisi'));
            })
            ->whereHas('user', function (Builder $userQuery): void {
                $userQuery
                    ->where('role', 'user')
                    ->where('payment_status', 'approved');
            });
    }
}

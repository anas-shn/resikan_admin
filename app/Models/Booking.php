<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_number',
        'user_id',
        'cleaner_id',
        'scheduled_at',
        'duration_minutes',
        'total_price',
        'status',
        'address',
        'location',
        'extras',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'location' => 'array',
        'extras' => 'array',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function cleaner()
    // {
    //     return $this->belongsTo(Cleaner::class);
    // }

    // public function bookingItems()
    // {
    //     return $this->hasMany(BookingItem::class);
    // }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}

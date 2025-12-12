<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Cleaner extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'employee_code',
        'fullname',
        'phone',
        'email',
        'status',
        'availability',
        'notes',
        'hired_at',
    ];

    protected $casts = [
        'availability' => 'array',
        'hired_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}

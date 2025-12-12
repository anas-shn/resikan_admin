<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'phone',
        'address',
        'metadata',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_hash' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the password attribute (maps to password_hash in Supabase).
     */
    public function getAuthPassword()
    {
        return $this->password_hash ?? $this->password;
    }

    /**
     * Set the password attribute (saves to both password and password_hash).
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
        $this->attributes['password_hash'] = $value;
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Only allow admin@resikan.com to access admin panel
        return $this->email === 'admin@resikan.com';
    }

    /**
     * Get the name attribute (maps fullname for Filament compatibility).
     */
    public function getNameAttribute(): ?string
    {
        return $this->fullname;
    }

    /**
     * Get the user's display name for Filament.
     */
    public function getFilamentName(): string
    {
        return $this->fullname ?? $this->email ?? 'User';
    }

    /**
     * Relationships
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}

<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'kampus',
        'cawangan',
        'fakulti_bahagian_id',
        'position',
        'is_superadmin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_superadmin' => 'boolean',
        ];
    }

    public function isSuperadmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    public function canWrite(): bool
    {
        return $this->is_superadmin || in_array($this->position, ['PTRJ', 'PRJ'], true);
    }

    public function fakultiBahagian(): BelongsTo
    {
        return $this->belongsTo(AvailableFakultiBahagian::class, 'fakulti_bahagian_id');
    }
}

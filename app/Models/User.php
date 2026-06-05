<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Allow automatic creation of factory instances
    use HasFactory, Notifiable;

    // Columns that can be filled by mass assignment
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'gender',
        'address',
        'phone',
        'role',
    ];

    // Hide sensitive fields when converting to array
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Define how to convert database values
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // A user has one staff record
    public function staffRecord()
    {
        return $this->hasOne(StaffRecord::class);
    }
}

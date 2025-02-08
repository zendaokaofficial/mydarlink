<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
        ];
    }

    // Relasi many-to-many dengan Proyek (anggota proyek)
    // User.php
    public function proyek()
    {
        return $this->belongsToMany(Proyek::class, 'proyek_user', 'user_id', 'proyek_id');
    }


    public function rencanaKinerjas()
    {
        return $this->belongsToMany(RencanaKinerja::class, 'rencana_kinerja_user', 'user_id', 'rencana_kinerja_id')
                    ->withTimestamps();
    }
}

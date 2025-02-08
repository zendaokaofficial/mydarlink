<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    //
    protected $fillable = [
        'tahun',
        'rencana_kinerja',
        'nama_proyek',
        'iku_id',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        // Saat proyek dibuat, otomatis isi user_id dengan user yang sedang login
        static::creating(function ($proyek) {
            $proyek->user_id = Auth::id();
        });
    }

    public function iku()
    {
        return $this->belongsTo(Iku::class, 'iku_id');
    }

    // Relasi many-to-many dengan User (anggota proyek)
    // Proyek.php
    public function anggota()
    {
        return $this->belongsToMany(User::class, 'proyek_user', 'proyek_id', 'user_id')->withTimestamps();
    }


    public function rencanaKinerjas()
    {
        return $this->hasMany(RencanaKinerja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    //
    protected $fillable = [
        'judul',
        'url',
        'deskripsi',
        'klasifikasi',
        'kategori',
    ];
}

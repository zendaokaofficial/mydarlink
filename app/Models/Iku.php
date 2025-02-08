<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iku extends Model
{
    //
    protected $fillable = [
        'tahun',
        'sasaran',
        'iku',
        'target',
        'satuan',
    ];

    public function proyek()
    {
        return $this->hasMany(Proyek::class);
    }
}

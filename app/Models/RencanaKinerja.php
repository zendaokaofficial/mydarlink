<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaKinerja extends Model
{
    //
    protected $fillable = [
        'rencana_kinerja',
        'description',
        'start_at',
        'end_at',
        'tempat',
        'tempat_lainnya',
        'kategori',
        'target',
        'satuan',
        'realisasi',
        'daftar_hadir',
        'rekap_daftar_hadir',
        'link_materi',
        'notulensi',
        'proyek_id',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'rencana_kinerja_user', 'rencana_kinerja_id', 'user_id')
                ->withTimestamps(); // Or another relationship type
    }
}

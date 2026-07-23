<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $fillable = [

        'user_id',
        'kategori_id',
        'judul',
        'isi_laporan',
        'foto',
        'status',

    ];

    // laporan dibuat oleh user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // laporan memiliki kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // laporan memiliki tanggapan
    public function tanggapan()
    {
        return $this->hasMany(Tanggapan::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function hasPhotoFile(): bool
    {
        if (! $this->foto) {
            return false;
        }

        return str_starts_with($this->foto, 'images/laporan/')
            ? is_file(public_path($this->foto))
            : Storage::disk('public')->exists($this->foto);
    }
}

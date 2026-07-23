<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukUmkm extends Model
{
    protected $table = 'produk_umkm';

    protected $fillable = ['user_id', 'nama_usaha', 'pemilik', 'kategori', 'nama_produk', 'deskripsi', 'foto_produk', 'kontak'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

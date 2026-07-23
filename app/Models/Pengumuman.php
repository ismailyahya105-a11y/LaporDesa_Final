<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = ['user_id', 'jenis', 'judul', 'isi', 'tanggal', 'aktif'];

    protected function casts(): array
    {
        return ['tanggal' => 'datetime', 'aktif' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

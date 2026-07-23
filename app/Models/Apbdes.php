<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apbdes extends Model
{
    protected $table = 'apbdes';

    protected $fillable = ['tahun', 'kategori', 'anggaran', 'realisasi'];

    protected function casts(): array
    {
        return ['anggaran' => 'decimal:2', 'realisasi' => 'decimal:2'];
    }
}

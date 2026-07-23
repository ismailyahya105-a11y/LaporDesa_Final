<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontakDesa extends Model
{
    protected $table = 'kontak_desa';

    protected $fillable = ['nama', 'jabatan', 'telepon', 'alamat'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanDarurat extends Model
{
    protected $table = 'laporan_darurat';

    protected $fillable = ['user_id', 'latitude', 'longitude', 'jenis_darurat', 'nomor_telepon', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usulan extends Model
{
    protected $table = 'usulan';

    protected $fillable = ['user_id', 'judul', 'isi', 'jumlah_vote'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voters()
    {
        return $this->belongsToMany(User::class, 'usulan_vote')->withTimestamps();
    }
}

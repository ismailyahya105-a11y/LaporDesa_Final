<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Polling extends Model
{
    protected $table = 'polling';

    protected $fillable = ['judul', 'deskripsi', 'berakhir_pada', 'aktif'];

    protected function casts(): array
    {
        return ['berakhir_pada' => 'datetime', 'aktif' => 'boolean'];
    }

    public function options()
    {
        return $this->hasMany(PollingOption::class);
    }

    public function voters()
    {
        return $this->belongsToMany(User::class, 'polling_vote')->withPivot('polling_option_id')->withTimestamps();
    }
}

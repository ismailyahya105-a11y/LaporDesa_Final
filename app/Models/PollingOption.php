<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingOption extends Model
{
    protected $table = 'polling_option';

    protected $fillable = ['polling_id', 'opsi'];

    public function polling()
    {
        return $this->belongsTo(Polling::class);
    }

    public function votes()
    {
        return $this->hasMany(PollingVote::class);
    }
}

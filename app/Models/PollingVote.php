<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingVote extends Model
{
    protected $table = 'polling_vote';

    protected $fillable = ['polling_id', 'polling_option_id', 'user_id'];

    public function option()
    {
        return $this->belongsTo(PollingOption::class, 'polling_option_id');
    }
}

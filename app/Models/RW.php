<?php

namespace App\Models;

use App\Models\Daerah;
use Illuminate\Database\Eloquent\Model;

class RW extends Model
{

    protected $table = 'rws';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class, 'daerah_id');
    }

    public function rts()
    {
        return $this->hasMany(RT::class);
    }
}

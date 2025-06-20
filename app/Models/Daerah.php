<?php

namespace App\Models;

use App\Models\RT;
use App\Models\RW;
use Illuminate\Database\Eloquent\Model;

class Daerah extends Model
{

    protected $guarded = ['id'];
    
    public function rws()
    {
        return $this->hasMany(RW::class);
    }

    public function rts()
    {
        return $this->hasMany(RT::class);
    }
}

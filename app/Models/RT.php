<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RT extends Model
{

    protected $table = 'rts';
    
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rwDetail()
    {
        return $this->belongsTo(RW::class, 'rw_id');
    }

    // public function rw()
    // {
    //     return $this->belongsTo(RW::class);
    // }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class, 'daerah_id');
    }
}

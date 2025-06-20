<?php

namespace App\Models;

use Carbon\Carbon;
use App\Filters\PendudukFilter;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penduduk extends Model
{
    // /** @use HasFactory<\Database\Factories\PendudukFactory> */
    use HasFactory;
    use LogsActivity;

    protected $table = 'penduduks';

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        // return kosong agar tidak log otomatis
        return LogOptions::defaults()
            ->logOnly([]) // Tidak log kolom apa pun
            ->dontSubmitEmptyLogs(); // Tidak mencatat log jika tidak ada perubahan
    }

    // Akses untuk menghitung umur berdasarkan tanggal_lahir
    public function getUmurAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rt()
    {
        return $this->belongsTo(RT::class, 'rt_id');
    }

    public function rw()
    {
        return $this->belongsTo(RW::class, 'rw_id');
    }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class, 'daerah_id');
    }

    // Fitur Filter
    public function scopeFilter($query, $request)
    {
        return (new PendudukFilter($request))->apply($query);
    }
}

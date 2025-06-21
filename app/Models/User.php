<?php

namespace App\Models;

use App\Models\RT;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use App\Models\Role;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable, CausesActivity;
    use LogsActivity;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([]) // Tidak log kolom apa pun
            ->dontSubmitEmptyLogs(); // Tidak mencatat log jika tidak ada perubahan
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke RW (jika user ini adalah RT)
    public function rws()
    {
        return $this->belongsTo(User::class, 'rw_id');
    }

    // Relasi ke data RT jika user ini adalah RW
    public function rts()
    {
        return $this->hasMany(User::class, 'rw_id');
    }

    // Jika pakai tabel rws/rts
    public function rwDetail()
    {
        return $this->hasOne(RW::class);
    }

    public function rtDetail()
    {
        return $this->hasOne(RT::class);
    }

    public function daerah()
    {
        return $this->belongsTo(Daerah::class, 'daerah_id');
    }

    public function penduduks()
    {
        return $this->hasMany(Penduduk::class);
    }

}

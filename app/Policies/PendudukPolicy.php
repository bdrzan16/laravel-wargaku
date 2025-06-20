<?php

namespace App\Policies;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PendudukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'rw', 'rt']);
        // return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Penduduk $penduduk): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('rw') && $penduduk->rt && $penduduk->rt->rw_id === $user->rw_id) {
            return true;
        }

        // if ($user->hasRole('rw') && $user->rw) {
        //     // Pastikan relasi lengkap dulu
        //     if (!$penduduk->rt || !$penduduk->rt->rw) {
        //         return false;
        //     }

        //     // Bandingkan rw_id
        //     return $penduduk->rt->rw->id === $user->rw->id;
        // }

        if ($user->hasRole('rt') && $penduduk->rt_id === $user->rt_id) {
            return true;
        }

        // if ($user->hasRole('rt') && $user->rt) {
        //     // cek apakah rt_id pada penduduk sesuai dengan rt milik user
        //     return $penduduk->rt_id == $user->rt->id;
        // }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
       return $user->hasAnyRole(['admin', 'rw', 'rt']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Penduduk $penduduk): bool
    {
        return $this->view($user, $penduduk);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Penduduk $penduduk): bool
    {
        if ($user->hasRole('admin')) {
            return true; // Admin bisa hapus semua
        }

        if ($user->hasRole('rw')) {
            return $penduduk->rt &&
                $penduduk->rt->rw_id === $user->rw_id &&
                $penduduk->rt->rwDetail->daerah_id === $user->daerah_id;
        }

        if ($user->hasRole('rt')) {
            return $penduduk->rt_id === $user->rt_id &&
                $penduduk->rt->rw_id === $user->rw_id &&
                $penduduk->rt->rwDetail->daerah_id === $user->daerah_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Penduduk $penduduk): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Penduduk $penduduk): bool
    {
        return false;
    }
}

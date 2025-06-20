<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Penduduk;
use App\Policies\PendudukPolicy;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Penduduk::class => PendudukPolicy::class,
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-activity', function (User $user, Activity $activity) {
            if ($user->hasRole('admin')) return true;

            $causer = $activity->causer;

            if (!$causer) return false;

            // RW: Bisa lihat dirinya sendiri & RT di wilayahnya
            if ($user->hasRole('rw')) {
                return (
                    $causer->id === $user->id && $causer->hasRole('rw')
                ) || (
                    $causer->hasRole('rt') &&
                    $causer->rw_id === $user->rw_id &&
                    $causer->daerah_id === $user->daerah_id
                );
            }

            // RT: Hanya bisa lihat dirinya sendiri
            if ($user->hasRole('rt')) {
                return $causer->id === $user->id && $causer->hasRole('rt');
            }

            return false;
        });

        // Gate::define('view-activity', function ($user, Activity $activity) {
        //     // Admin bisa melihat semua aktivitas
        //     if ($user->hasRole('admin')) {
        //         return true;
        //     }

        //     $causer = $activity->causer;
        //     if (!$causer) return false;

        //     // RW bisa lihat aktivitas dirinya sendiri dan RT dalam RW/daerah yang sama
        //     if ($user->hasRole('rw')) {
        //         return (
        //             // RW melihat dirinya sendiri
        //             ($causer->id === $user->id && $causer->hasRole('rw')) ||

        //             // RW melihat RT dalam wilayah yang sama
        //             ($causer->hasRole('rt') &&
        //             $causer->rw == $user->rw &&
        //             $causer->daerah == $user->daerah)
        //         );
        //     }

        //     // RT hanya bisa lihat dirinya sendiri
        //     if ($user->hasRole('rt')) {
        //         return $activity->causer_id === $user->id;
        //     }

        //     // Default: ditolak
        //     return false;
        // });
    }
}

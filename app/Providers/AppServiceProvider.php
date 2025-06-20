<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
// use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
// use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade::if('role', function ($role) {
        //     return auth()->check() && auth()->user()->hasRole($role);
        // });
        // $this->registerPolicies($gate);

        // $gate->define('admin', function (User $user) {
        //     return $user->role_id === 1;
        // });

        // $gate->define('operator_input', function (User $user) {
        //     return $user->role_id === 2;
        // });
        // $gate->define('viewer', function (User $user) {
        //     return $user->role_id === 3;
        // });

        App::setLocale('id');
        Carbon::setLocale('id');
        Paginator::useBootstrapFour();
    }
}

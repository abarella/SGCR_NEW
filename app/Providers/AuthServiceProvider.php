<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // PSP-PS Permissions
        Gate::define('view-psp-ps', function ($user) {
            return true; // Todos podem ver
        });

        Gate::define('edit-psp-ps', function ($user) {
            return session('cdgrupo') == 6; // Apenas grupo 6
        });

        Gate::define('edit-psp-ps-doc', function ($user) {
            return in_array(session('cdgrupo'), [2,3,4,5,6]); // Grupos 2 a 6
        });
    }
}

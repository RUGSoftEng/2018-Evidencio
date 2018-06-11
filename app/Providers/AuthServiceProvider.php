<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Workflow;
use App\Policies\WorkflowPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Workflow::class => WorkflowPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-designer', function($user) {
            return $user->is_verified && $user->email_verified;
        });

        Gate::define('not-view-designer', function($user) {
            return !$user->is_verified || !$user->email_verified;
        });

        Gate::define('is-administrator', function($user) {
            return $user->is_administrator;
        });
    }
}

<?php

namespace App\Providers;

use Laravel\Passport\Passport; 
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes(); 
        Passport::tokensExpireIn(Carbon::now()->addHours(6));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(2));
        Passport::personalAccessTokensExpireIn(Carbon::now()->addHours(6));
        Passport::tokensCan([
            'default-user' => 'Default user',
            'default-admin' => 'Default admin',
        ]);

        //used for JavaScript or mobile applications where the client credentials can't be securely stored
        //Passport::enableImplicitGrant();
    }
}

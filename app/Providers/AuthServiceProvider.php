<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Animal;
use App\Policies\AnimalPolicy;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Animal::class => AnimalPolicy::class,
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
        
        //access_token expired in 15days
        Passport::tokensExpireIn(now()->addDays(15));

        //refresh_token expired in 30days
        Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::tokensCan([
            'create-animals' => '建立動物資訊',
            'user-info' => '使用者資訊'
        ]);
    }
}

<?php

namespace App\Providers;

use App\Models\Team;
use App\Services\Azure\Auth\AssignRoles;
use Illuminate\Support\ServiceProvider;

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
        \Metrogistics\AzureSocialite\UserFactory::userCallback(function($new_user){
            $new_user->save();
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Business\Calendar\Provider\Google\GoogleEventStore;
use App\Business\Calendar\Provider\AccessTokenStorageInterface;
use App\Business\Calendar\Provider\UserAccessTokenStorage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(GoogleEventStore::class, function(){
            return new GoogleEventStore(json_decode(file_get_contents(storage_path('app/credentials.json')), true),
             route('google.oauth.callback'), 
             app(AccessTokenStorageInterface::class));
        });

        app()->singleton(AccessTokenStorageInterface::class, UserAccessTokenStorage::class);
    }
}

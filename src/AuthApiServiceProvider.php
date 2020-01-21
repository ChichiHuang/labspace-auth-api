<?php 
namespace Labspace\AuthApi;

use Illuminate\Support\ServiceProvider;

class AuthApiServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //融合route
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        //新增config
        $this->publishes([
            __DIR__.'/../config/labspace-auth-api.php' => config_path('labspace-auth-api.php')
        ], 'config');

        //新增migration
        $this->publishes([
            __DIR__.'/../migration/2019_05_29_002100_create_social_accounts_table.php' => database_path('migrations/2019_05_29_002100_create_social_accounts_table.php')
        ], 'migration-social');



    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/labspace-auth-api.php', 'labspace-auth-api'
        );
    }



}

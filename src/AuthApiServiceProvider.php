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

        $this->loadViewsFrom(__DIR__.'/views', 'labspace-auth-api');

        //新增config
        $this->publishes([
            __DIR__.'/../config/labspace-auth-api.php' => config_path('labspace-auth-api.php')
        ], 'config');

        //新增migration 社群登入
        $this->publishes([
            __DIR__.'/../migration/2019_05_29_002100_create_social_accounts_table.php' => database_path('migrations/2019_05_29_002100_create_social_accounts_table.php')
        ], 'migration-social');

        //新增migration 重置密碼
        $this->publishes([
            __DIR__.'/../migration/2014_10_12_100000_create_password_resets_table.php' => database_path('migrations/2014_10_12_100000_create_password_resets_table.php')
        ], 'migration-password-reset');

        //新增view
        $this->publishes([
            __DIR__.'/views'  => base_path('resources/views/vendor/labspace-auth-api'),
        ], 'view');

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

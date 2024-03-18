<?php
namespace YourNamespace\YourPackageName;

use Illuminate\Support\ServiceProvider;

class YourServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'laravel/database/migrations');
    }

    public function register()
    {
        //
    }
}

<?php

namespace Enbolt\Emt;

use Illuminate\Support\ServiceProvider;

class EmtServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'emt');
        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/Theme/Admin'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Enbolt\Emt\Http\Controllers\EmtController');
    }
}

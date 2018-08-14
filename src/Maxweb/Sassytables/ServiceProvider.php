<?php

namespace Maxweb\Sassytables;

use Maxweb\Sassytables\Sassytable;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Sassytable::class, function ($app) {
            $sassyTable = new Sassytable();
            return $sassyTable;
        });

        $this->app->alias(Sassytable::class, 'sassytable');
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'sassytables');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/sassytables'),
        ]);
    }
}
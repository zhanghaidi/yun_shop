<?php

namespace app\common\providers;

use app\framework\Bus\Dispatcher;

class BusServiceProvider extends \Illuminate\Bus\BusServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Illuminate\Bus\Dispatcher', function ($app) {
            return new Dispatcher($app, function ($connection = null) use ($app) {
                return $app['Illuminate\Contracts\Queue\Factory']->connection($connection);
            });
        });

        $this->app->alias(
            'Illuminate\Bus\Dispatcher', 'Illuminate\Contracts\Bus\Dispatcher'
        );
        $this->app->alias(
            'Illuminate\Bus\Dispatcher', 'Illuminate\Contracts\Bus\QueueingDispatcher'
        );
    }
}
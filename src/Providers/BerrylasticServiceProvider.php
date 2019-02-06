<?php

namespace Imdhemy\Berrylastic\Providers;

use Elasticsearch\Client;
use Illuminate\Support\ServiceProvider;

class BerrylasticServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('imdhemy.berrylastic', function () {
            return new Client;
        });

        $this->app->alias('imdhemy.berrylastic', Client::class);
    }
}

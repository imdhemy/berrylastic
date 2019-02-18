<?php
namespace Imdhemy\Berrylastic\Facades;

use Illuminate\Support\Facades\Facade;

class Client extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'imdhemy.berrylastic';
    }
}

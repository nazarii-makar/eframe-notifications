<?php

namespace EFrame\Notifications\Facades;

use Illuminate\Support\Facades\Facade;
use EFrame\Notifications\Clients\SmsFlyClient;

class SmsFly extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SmsFlyClient::class;
    }
}
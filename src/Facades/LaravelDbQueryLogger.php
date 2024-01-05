<?php

namespace EyadBereh\LaravelDbQueryLogger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \EyadBereh\LaravelDbQueryLogger\LaravelDbQueryLogger
 */
class LaravelDbQueryLogger extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \EyadBereh\LaravelDbQueryLogger\LaravelDbQueryLogger::class;
    }
}

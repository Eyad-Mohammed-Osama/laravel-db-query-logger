<?php

namespace EyadBereh\LaravelDbQueryLogger;

use EyadBereh\LaravelDbQueryLogger\Drivers\AbstractDriver;

class LaravelDbQueryLogger
{
    private AbstractDriver $driver;

    public function __construct()
    {
        $this->driver = app(AbstractDriver::class);
    }

    public function getLogs(?string $date = null, bool $dates_as_keys = true) : array|null {
        return $this->driver->getLogs($date, $dates_as_keys);
    }
}

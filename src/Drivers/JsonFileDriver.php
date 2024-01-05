<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

class JsonFileDriver extends AbstractDriver
{
    public function log(): void
    {
        echo "Hello from JsonFileDriver";
    }
}

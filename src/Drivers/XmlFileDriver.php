<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

class XmlFileDriver extends AbstractDriver
{
    public function log(): void
    {
        echo 'Hello from XmlFileDriver';
    }
}

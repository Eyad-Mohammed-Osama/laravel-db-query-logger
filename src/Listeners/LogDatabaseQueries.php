<?php

namespace EyadBereh\LaravelDbQueryLogger\Listeners;

use EyadBereh\LaravelDbQueryLogger\Drivers\AbstractDriver;
use Illuminate\Database\Events\QueryExecuted;

class LogDatabaseQueries
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QueryExecuted $event): void
    {
        $driver = app(AbstractDriver::class);
        $driver->setEvent($event);
        $driver->store();
    }
}

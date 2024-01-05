<?php

namespace EyadBereh\LaravelDbQueryLogger\Listeners;

use EyadBereh\LaravelDbQueryLogger\Drivers\AbstractDriver;
use Illuminate\Database\Events\QueryExecuted;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Utils\Query;

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

        // $parser = new Parser($event->sql);
        // $statement = $parser->statements[0];
        // $data = Query::getAll($event->sql);
        // dd($data["statement"]);
    }
}

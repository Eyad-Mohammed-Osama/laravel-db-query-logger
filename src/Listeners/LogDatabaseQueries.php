<?php

namespace EyadBereh\LaravelDbQueryLogger\Listeners;

use EyadBereh\LaravelDbQueryLogger\Drivers\AbstractDriver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Token;
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

<?php

namespace EyadBereh\LaravelDbQueryLogger\Commands;

use Illuminate\Console\Command;

class LaravelDbQueryLoggerCommand extends Command
{
    public $signature = 'laravel-db-query-logger';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

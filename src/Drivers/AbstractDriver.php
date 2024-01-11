<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

use EyadBereh\LaravelDbQueryLogger\Enums\SqlStatements;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\App;
use PhpMyAdmin\SqlParser\Utils\Query;

abstract class AbstractDriver
{
    protected QueryExecuted $event;

    abstract protected function log(): void;

    public function store()
    {
        $environment = App::environment();
        $is_enabled = config("db-query-logger.enabled");
        if (!$is_enabled) {
            return;
        }

        $info = $this->getQueryInfo();
        $type = $info['type'];
        $statement_type = SqlStatements::tryFrom($type);
        $statement_types = config('db-query-logger.statements');
        $query_time_threshold = config('db-query-logger.query_time_threshold');
        $connections = config('db-query-logger.connections');

        if (!in_array($statement_type, $statement_types)) {
            return;
        }

        if (!is_null($query_time_threshold) && $this->event->time < $query_time_threshold) {
            return;
        }

        if (!is_null($connections) && !in_array($this->event->connectionName, $connections)) {
            return;
        }

        $this->log();
    }

    final public function setEvent(QueryExecuted $event): void
    {
        $this->event = $event;
    }

    final protected function getQueryInfo()
    {
        $query = $this->event->sql;
        $info = Query::getAll($query);

        return [
            'type' => $info['querytype'],
        ];
        // return Query::getAll($query);
    }
}

<?php

// config for EyadBereh/LaravelDbQueryLogger

use EyadBereh\LaravelDbQueryLogger\Drivers\JsonFileDriver;
use EyadBereh\LaravelDbQueryLogger\Drivers\LogFileDriver;
use EyadBereh\LaravelDbQueryLogger\Enums\SqlStatements;

return [
    'enabled' => env('LARAVEL_DB_QUERY_LOGGER_ENABLED', true),

    'environments' => env('LARAVEL_DB_QUERY_LOGGER_ENVIRONMENTS') ? explode(",", env('LARAVEL_DB_QUERY_LOGGER_ENVIRONMENTS')) : NULL, // an array of environments

    // defines the threshold of time a query takes to execute
    // so that it can be logged, in milliseconds, and nullable
    'query_time_threshold' => null,

    'connections' => null, // an array of database connections to listen to, by default it listens to all connections

    // the type of statements to be filtered
    'statements' => [
        SqlStatements::SELECT,
        SqlStatements::INSERT,
        SqlStatements::UPDATE,
        SqlStatements::DELETE,
    ],

    'driver' => env('LARAVEL_DB_QUERY_LOGGER_DRIVER', 'log_file'),

    'drivers' => [
        'log_file' => [
            'concrete' => LogFileDriver::class,
            'path' => storage_path('db-query-logger'),
            'message_format' => '[:datetime:] - [:statement_type:] - [query = :query:] - [bindings = :bindings:] - [time = :time: ms] - [connection = :connection:]',
            'use_laravel_logs' => false
        ],
        'json_file' => [
            'concrete' => JsonFileDriver::class,
            'path' => storage_path('db-query-logger'),
            'schema' => [
                'datetime' => ":datetime:",
                'statement_type' => ":statement_type:",
                'query' => ":query:",
                'bindings' => ":bindings:",
                'time' => ":time:",
                'connection' => ":connection:",
            ]
        ],
    ],
];

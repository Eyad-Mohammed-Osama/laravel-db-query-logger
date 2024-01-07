<?php

// config for EyadBereh/LaravelDbQueryLogger

use EyadBereh\LaravelDbQueryLogger\Drivers\JsonFileDriver;
use EyadBereh\LaravelDbQueryLogger\Drivers\LogFileDriver;
use EyadBereh\LaravelDbQueryLogger\Enums\SqlStatements;

return [
    'enabled' => env('LARAVEL_DB_QUERY_LOGGER_ENABLED', true), // you can also specify an array of environments if you want

    // defines the threshold of time a query takes to execute
    // so that it can be logged, in milliseconds, and nullable
    'query_time_threshold' => 1000,

    // 'include_tables' => null, // you can define an array of tables to log, or leave as null

    // // tables to exclude from logging, it will be ignored if the "include_tables" option is set
    // 'exclude_tables' => [
    //     'migrations',
    //     'jobs',
    //     'failed_jobs',
    // ],

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
        ],
        'json_file' => [
            'concrete' => JsonFileDriver::class,
            'path' => storage_path('db-query-logger'),
        ]
    ],
];

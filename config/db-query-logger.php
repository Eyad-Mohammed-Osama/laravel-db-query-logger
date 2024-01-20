<?php

// config for EyadBereh/LaravelDbQueryLogger

use EyadBereh\LaravelDbQueryLogger\Drivers\ArrayDriver;
use EyadBereh\LaravelDbQueryLogger\Drivers\CacheDriver;
use EyadBereh\LaravelDbQueryLogger\Drivers\JsonFileDriver;
use EyadBereh\LaravelDbQueryLogger\Drivers\LogFileDriver;
use EyadBereh\LaravelDbQueryLogger\Drivers\TelegramDriver;
use EyadBereh\LaravelDbQueryLogger\Drivers\WebhookDriver;
use EyadBereh\LaravelDbQueryLogger\Enums\SqlStatements;

return [
    'enabled' => env('LARAVEL_DB_QUERY_LOGGER_ENABLED', true),

    'environments' => env('LARAVEL_DB_QUERY_LOGGER_ENVIRONMENTS') ? explode(',', env('LARAVEL_DB_QUERY_LOGGER_ENVIRONMENTS')) : null, // an array of environments

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
            'use_laravel_logs' => false,
        ],
        'json_file' => [
            'concrete' => JsonFileDriver::class,
            'path' => storage_path('db-query-logger'),
            'schema' => [
                'datetime' => ':datetime:',
                'statement_type' => ':statement_type:',
                'query' => ':query:',
                'bindings' => ':bindings:',
                'time' => ':time:',
                'connection' => ':connection:',
            ],
        ],
        'array' => [
            'concrete' => ArrayDriver::class
        ],
        'cache' => [
            'concrete' => CacheDriver::class,
            'store' => env("LARAVEL_DB_QUERY_LOGGER_CACHE_STORE") ?? env("CACHE_DRIVER"),
            'key_prefix' => env("LARAVEL_DB_QUERY_LOGGER_CACHE_KEY_PREFIX", "db-query-logger-")
        ],
        'webhook' => [
            'concrete' => WebhookDriver::class,
            'callback_url' => env("LARAVEL_DB_QUERY_LOGGER_WEBHOOK_CALLBACK_URL"),
            'method' => 'POST',
            'secret' => [
                'header' => 'X-Query-Logger-Token',
                'value' => env("LARAVEL_DB_QUERY_LOGGER_WEBHOOK_TOKEN")
            ],
            'headers' => [
                // send any additional headers with the request
            ],
            'data' => [
                // send any additional data with the request
            ]
        ],
        'telegram' => [
            'concrete' => TelegramDriver::class,
            'message_format' => 'Datetime: [:datetime:]\nStatement Type:[:statement_type:]\nSQL Query: [:query:]\nBindings: [:bindings:]\nExecution Time: [:time: ms]\nConnection Name: [:connection:]',
            'bot_token' => env('LARAVEL_DB_QUERY_LOGGER_TELEGRAM_BOT_TOKEN'),
            'chat_ids' => env('LARAVEL_DB_QUERY_LOGGER_TELEGRAM_CHAT_IDS') ? explode(',', env('LARAVEL_DB_QUERY_LOGGER_TELEGRAM_CHAT_IDS')) : null
        ]
    ],
];

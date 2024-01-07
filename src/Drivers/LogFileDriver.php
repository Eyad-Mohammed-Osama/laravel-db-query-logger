<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

use Illuminate\Support\Facades\File;

class LogFileDriver extends AbstractDriver
{
    public function log(): void
    {
        $date = now()->format('Y-m-d');
        $path = config('db-query-logger.drivers.log_file.path');
        $filename = "$date.log";
        $fullpath = "$path/$filename";
        $content = $this->getCompiledMessage() . PHP_EOL;

        File::ensureDirectoryExists($path);

        if (!File::exists($fullpath)) {
            File::put($fullpath, $content);
        } else {
            File::append($fullpath, $content);
        }
    }

    private function getCompiledMessage()
    {
        $info = $this->getQueryInfo(); // obtain query information

        $format = "[:datetime:] - [:statement_type:] - [query = :query:] - [bindings = :bindings:] - [time = :time: ms] - [connection = :connection:]"; // get log message format

        // prepare placeholders data for compilation
        $data = [
            ':datetime:' => now()->format('Y-m-d H:i:s'),
            ':statement_type:' => $info['type'],
            ':query:' => $this->event->sql,
            ':bindings:' => json_encode($this->event->bindings),
            ':time:' => $this->event->time,
            ':connection:' => $this->event->connectionName,
        ];

        return strtr($format, $data); // compile and return the formatted message
    }
}

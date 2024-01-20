<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogFileDriver extends AbstractDriver
{
    public function writeLog(): void
    {
        $content = $this->getCompiledMessage();
        $use_laravel_logs = config('db-query-logger.drivers.log_file.use_laravel_logs');

        if ($use_laravel_logs) {
            Log::notice($content);
        } else {
            $date = now()->format('Y-m-d');
            $path = config('db-query-logger.drivers.log_file.path');
            $filename = "$date.log";
            $fullpath = "$path/$filename";

            File::ensureDirectoryExists($path);

            $content = $content."\n";
            if (! File::exists($fullpath)) {
                File::put($fullpath, $content);
            } else {
                File::append($fullpath, $content);
            }
        }
    }

    private function getCompiledMessage()
    {
        $info = $this->getQueryInfo(); // obtain query information

        $format = config('db-query-logger.drivers.log_file.message_format'); // get log message format

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

    public function getLogs(?string $date = null): ?array
    {
        // TODO: Implement getLogs() method.
        return null;
    }
}

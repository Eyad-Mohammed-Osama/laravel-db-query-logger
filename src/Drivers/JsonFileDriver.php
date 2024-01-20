<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class JsonFileDriver extends AbstractDriver
{
    public function writeLog(): void
    {
        $date = now()->format('Y-m-d');
        $path = config('db-query-logger.drivers.json_file.path');
        $filename = "$date.json";
        $fullpath = "$path/$filename";
        $content = $this->getJsonObject();

        File::ensureDirectoryExists($path);

        if (! File::exists($fullpath)) {
            $content = [$content];
            $content = json_encode($content, JSON_PRETTY_PRINT);
            File::put($fullpath, $content);
        } else {
            // What the hell are you doing, my uncle ?
            $file = fopen($fullpath, 'a+');
            $stats = fstat($file);
            $size = $stats['size'];
            ftruncate($file, $size - 1);
            fclose($file);

            $file = fopen($fullpath, 'a');
            $content = json_encode($content, JSON_PRETTY_PRINT);
            fwrite($file, ",\n".$content."\n]");
            fclose($file);
        }
    }

    public function getLogs(?string $date = null, bool $dates_as_keys = true): array|null
    {
        if ($date) {
            return $this->getLog($date);
        }
        $path = config('db-query-logger.drivers.json_file.path');
        $files = File::files($path);
        $contents = [];

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            if ($dates_as_keys) {
                $contents[$filename] = $this->getLog($filename);
            }
            else {
                $logs = $this->getLog($filename);
                foreach($logs as $log) {
                    $contents[] = $log;
                }
            }
        }
        return $contents;
    }

    private function getLog(string $date): array|null
    {
        $filename = "$date.json";
        $path = config('db-query-logger.drivers.json_file.path');
        $fullpath = "$path/$filename";
        if (!File::exists($fullpath)) {
            return null;
        }

        $contents = File::get($fullpath);

        $validator = Validator::make([
            "contents" => $contents
        ], [
            "contents" => ["json"]
        ]);

        if ($validator->fails()) {
            throw new \Exception("File [$filename] at [$path] is not a valid JSON file.");
        }

        $contents = json_decode($contents, true);
        return $contents;
    }

    private function getJsonObject()
    {
        $info = $this->getQueryInfo(); // obtain query information

        $data = [
            'datetime' => now()->format('Y-m-d H:i:s'),
            'statement_type' => $info['type'],
            'query' => $this->event->sql,
            'bindings' => $this->event->bindings,
            'time' => $this->event->time,
            'connection' => $this->event->connectionName,
        ];

        $schema = config('db-query-logger.drivers.json_file.schema');

        $object = $this->compileJsonSchema($schema, $data);

        return $object; // compile and return the formatted message
    }

    private function compileJsonSchema($schema, $data)
    {
        $compiled_schema = [];
        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $compiled_schema[$key] = $this->compileJsonSchema($value, $data);
            } else {
                $value = $data[$key];
                $compiled_schema[$key] = $value;
            }
        }

        return $compiled_schema;
    }

}

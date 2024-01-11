<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

use Illuminate\Support\Facades\File;

class JsonFileDriver extends AbstractDriver
{
    public function log(): void
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

        $schema = config("db-query-logger.drivers.json_file.schema");

        $object = $this->compileJsonSchema($schema, $data);        

        return $object; // compile and return the formatted message
    }

    private function compileJsonSchema($schema, $data) {
        $compiled_schema = [];
        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $compiled_schema[$key] = $this->compileJsonSchema($value, $data);
            }
            else {
                $value = $data[$key];
                $compiled_schema[$key] = $value;
            }
        }
        return $compiled_schema;
    }
}

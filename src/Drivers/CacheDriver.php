<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

class CacheDriver extends AbstractDriver
{
    protected function writeLog(): void
    {
        $store = config('db-query-logger.drivers.cache.store');
        $key_prefix = config('db-query-logger.drivers.cache.key_prefix');
        $cache = \Illuminate\Support\Facades\Cache::store($store);
        $key = date('Y-m-d');
        $full_key = $key_prefix.$key;
        $contents = $cache->get($full_key, []);
        $contents[] = $this->getObject();
        $cache->put($full_key, $contents);

        $available_keys = $cache->get($key_prefix.'available-keys', []);
        if (! in_array($key, $available_keys)) {
            $available_keys[] = $key;
            sort($available_keys);
            $cache->put($key_prefix.'available-keys', $available_keys);
        }
    }

    public function getLogs(?string $date = null): ?array
    {
        $store = config('db-query-logger.drivers.cache.store');
        $key_prefix = config('db-query-logger.drivers.cache.key_prefix');
        $cache = \Illuminate\Support\Facades\Cache::store($store);
        if (! is_null($date)) {
            $full_key = $key_prefix.$date;

            return $cache->get($full_key);
        }
        $contents = [];
        $available_keys = $cache->get($key_prefix.'available-keys', []);
        foreach ($available_keys as $key) {
            $full_key = $key_prefix.$key;
            $contents[$key] = $cache->get($full_key);
        }

        return count($contents) > 0 ? $contents : null;
    }

    private function getObject(): array
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

        return $data; // compile and return the formatted message
    }
}

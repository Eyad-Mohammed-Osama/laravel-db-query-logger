<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

class ArrayDriver extends AbstractDriver
{
    private static array $logs = [];
    protected function writeLog(): void
    {
        $log = $this->getLoggedObject();
        static::$logs[] = $log;
    }
    public function getLogs(?string $date = null): array|null
    {
        return count(static::$logs) > 0 ? static::$logs : null;
    }

    private function getLoggedObject(): array {
        $info = $this->getQueryInfo(); // obtain query information

        $data = [
            'datetime' => now()->format('Y-m-d H:i:s'),
            'statement_type' => $info['type'],
            'query' => $this->event->sql,
            'bindings' => $this->event->bindings,
            'time' => $this->event->time,
            'connection' => $this->event->connectionName,
        ];

        return $data;
    }
}
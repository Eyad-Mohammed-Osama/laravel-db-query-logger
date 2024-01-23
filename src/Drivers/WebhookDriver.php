<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

use Illuminate\Support\Str;

class WebhookDriver extends AbstractDriver
{
    protected function writeLog(): void
    {
        $callback_url = config('db-query-logger.drivers.webhook.callback_url');
        $method = Str::lower(config('db-query-logger.drivers.webhook.method'));
        $secret_header = config('db-query-logger.drivers.webhook.secret.header');
        $secret_value = config('db-query-logger.drivers.webhook.secret.value');
        $headers = config('db-query-logger.drivers.webhook.headers');
        $data = config('db-query-logger.drivers.webhook.data');

        if ($secret_value) {
            $headers = array_merge($headers, [
                $secret_header => $secret_value,
            ]);
        }

        $object = $this->getObject();
        $data = array_merge($object, $data);
        $client = \Illuminate\Support\Facades\Http::withHeaders($headers);
        $client->asForm();
        $client->{$method}($callback_url, $data);
    }

    public function getLogs(?string $date = null): ?array
    {
        return null;
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

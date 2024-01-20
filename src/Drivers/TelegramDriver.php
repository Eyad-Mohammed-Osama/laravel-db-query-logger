<?php

namespace EyadBereh\LaravelDbQueryLogger\Drivers;

class TelegramDriver extends AbstractDriver
{

    protected function writeLog(): void
    {
        $bot_token = config("db-query-logger.drivers.telegram.bot_token");
        $chat_ids = config("db-query-logger.drivers.telegram.chat_ids");
        $message = $this->getCompiledMessage();
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        foreach ($chat_ids as $chat_id) {
            \Illuminate\Support\Facades\Http::post($url, [
                "chat_id" => $chat_id,
                "text" => $message
            ]);
        }
    }

    public function getLogs(?string $date = null): array|null
    {
        return null;
    }

    private function getCompiledMessage()
    {
        $info = $this->getQueryInfo(); // obtain query information

        $format = config('db-query-logger.drivers.telegram.message_format'); // get log message format

        // prepare placeholders data for compilation
        $data = [
            ':datetime:' => now()->format('Y-m-d H:i:s'),
            ':statement_type:' => $info['type'],
            ':query:' => $this->event->sql,
            ':bindings:' => json_encode($this->event->bindings, JSON_PRETTY_PRINT),
            ':time:' => $this->event->time,
            ':connection:' => $this->event->connectionName,
        ];

        return strtr($format, $data); // compile and return the formatted message
    }
}
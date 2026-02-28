<?php

declare(strict_types=1);

namespace Core;

final class Response
{
    public static function json(mixed $data, int $status = 200)
    {
        header('Content-type: application/json');
        http_response_code($status);

        $payload = [
            "data" => $data,
            "error" => null,
        ];

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    public static function error(string $message, int $status = 500)
    {
        header('Content-type: application/json; charset=utf-8');
        http_response_code($status);

        $payload = [
            "data" => null,
            "error" => $message
        ];

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    public static function getBody()
    {
        return json_decode(file_get_contents('php://input', true) ?? null);
    }
}

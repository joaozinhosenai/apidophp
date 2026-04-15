<?php

// =============================================================
//  app/core/Response.php  –  Helper para respostas JSON
// =============================================================

class Response
{
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function success(mixed $data, string $message = 'OK', int $code = 200): void
    {
        self::json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public static function error(string $message, int $code = 400, mixed $detail = null): void
    {
        $payload = ['status' => 'error', 'message' => $message];
        if ($detail !== null) $payload['detail'] = $detail;
        self::json($payload, $code);
    }
}
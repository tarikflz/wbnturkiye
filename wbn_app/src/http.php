<?php
declare(strict_types=1);

final class HttpError extends Exception
{
    public function __construct(int $status, string $message, public array $errors = [])
    {
        parent::__construct($message, $status);
    }
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

/** JSON veya form gövdesini dizi olarak döner. */
function request_body(): array
{
    $type = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_starts_with($type, 'application/json')) {
        $data = json_decode((string) file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }
    return $_POST;
}

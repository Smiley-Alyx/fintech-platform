<?php

declare(strict_types=1);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($method === 'GET' && $path === '/health') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['data' => ['status' => 'ok']], JSON_THROW_ON_ERROR);
    exit;
}

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(503);
    header('Content-Type: application/json');
    echo json_encode(['error' => ['code' => 'service_unavailable']], JSON_THROW_ON_ERROR);
    exit;
}

require $autoload;
$rawBody = file_get_contents('php://input');
if ($rawBody === false) {
    $rawBody = '';
}

$headers = [];
foreach ($_SERVER as $key => $value) {
    if (!is_string($value)) {
        continue;
    }

    if (str_starts_with($key, 'HTTP_')) {
        $name = str_replace('_', '-', substr($key, 5));
        $headers[$name] = $value;
    }
}

$kernel = new App\Infrastructure\Http\Kernel();
$response = $kernel->handle($method, $path, $rawBody, $headers);

foreach ($response->headers as $name => $value) {
    header($name . ': ' . $value);
}

http_response_code($response->statusCode);
echo json_encode($response->body, JSON_THROW_ON_ERROR);

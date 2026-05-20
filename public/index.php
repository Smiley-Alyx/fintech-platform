<?php

declare(strict_types=1);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($method === 'GET' && $path === '/health') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok'], JSON_THROW_ON_ERROR);
    exit;
}

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(503);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'service_unavailable'], JSON_THROW_ON_ERROR);
    exit;
}

require $autoload;
$rawBody = file_get_contents('php://input');
if ($rawBody === false) {
    $rawBody = '';
}

$kernel = new App\Infrastructure\Http\Kernel();
$response = $kernel->handle($method, $path, $rawBody);

foreach ($response->headers as $name => $value) {
    header($name . ': ' . $value);
}

http_response_code($response->statusCode);
echo json_encode($response->body, JSON_THROW_ON_ERROR);

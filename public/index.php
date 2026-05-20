<?php

declare(strict_types=1);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

header('Content-Type: application/json');

if ($method === 'GET' && $path === '/health') {
    http_response_code(200);
    echo json_encode(['status' => 'ok'], JSON_THROW_ON_ERROR);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'not_found'], JSON_THROW_ON_ERROR);

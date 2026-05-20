<?php

declare(strict_types=1);

$sendJson = static function (int $statusCode, array $payload): void {
    http_response_code($statusCode);
    echo json_encode($payload, JSON_THROW_ON_ERROR);
};

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

header('Content-Type: application/json');

if ($method === 'GET' && $path === '/health') {
    $sendJson(200, ['status' => 'ok']);
    exit;
}

if ($method === 'POST' && $path === '/transactions/authorize') {
    try {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (!is_file($autoload)) {
            $sendJson(503, ['error' => 'service_unavailable']);
            exit;
        }
        require $autoload;

        $rawBody = file_get_contents('php://input');
        if ($rawBody === false) {
            $sendJson(400, ['error' => 'invalid_body']);
            exit;
        }

        $data = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            $sendJson(400, ['error' => 'invalid_json']);
            exit;
        }

        foreach (['card_id', 'external_transaction_id', 'amount', 'vendor_id'] as $key) {
            if (!array_key_exists($key, $data)) {
                $sendJson(422, ['error' => 'validation_failed', 'field' => $key]);
                exit;
            }
        }

        $databaseUrl = getenv('DATABASE_URL');
        if (!is_string($databaseUrl) || $databaseUrl === '') {
            $sendJson(503, ['error' => 'service_unavailable']);
            exit;
        }

        $cfg = App\Infrastructure\Persistence\Postgres\DatabaseUrl::parse($databaseUrl);
        $pdo = App\Infrastructure\Persistence\Postgres\PdoConnectionFactory::createFromDsn($cfg->dsn, $cfg->user, $cfg->password);

        $uow = new App\Infrastructure\Persistence\Postgres\PdoUnitOfWork($pdo);
        $cards = new App\Infrastructure\Persistence\Postgres\PdoCardRepository($pdo);
        $accounts = new App\Infrastructure\Persistence\Postgres\PdoBankAccountRepository($pdo);
        $transactions = new App\Infrastructure\Persistence\Postgres\PdoTransactionRepository($pdo);

        $handler = new App\Application\Transactions\AuthorizeTransaction\AuthorizeTransactionHandler(
            $uow,
            $cards,
            $accounts,
            $transactions,
        );

        $controller = new App\Http\TransactionController($handler);

        $request = new App\Http\AuthorizeTransactionRequest(
            card_id: (int) $data['card_id'],
            external_transaction_id: (string) $data['external_transaction_id'],
            amount: (string) $data['amount'],
            vendor_id: (string) $data['vendor_id'],
        );

        $resource = $controller->authorize($request);
        $sendJson(200, $resource->toArray());
        exit;
    } catch (App\Domain\Transaction\TransactionException $e) {
        $sendJson($e->statusCode, ['error' => $e->reason->value]);
        exit;
    } catch (\JsonException) {
        $sendJson(400, ['error' => 'invalid_json']);
        exit;
    } catch (Throwable) {
        $sendJson(500, ['error' => 'internal_error']);
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'not_found'], JSON_THROW_ON_ERROR);

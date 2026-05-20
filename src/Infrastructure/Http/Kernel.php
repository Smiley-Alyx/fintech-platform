<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\HmacSignatureValidator;
use App\Application\Transactions\AuthorizeTransaction\AuthorizeTransactionHandler;
use App\Domain\Transaction\TransactionException;
use App\Http\AuthorizeTransactionRequest;
use App\Http\TransactionController;
use App\Infrastructure\Persistence\Postgres\DatabaseUrl;
use App\Infrastructure\Persistence\Postgres\PdoBankAccountRepository;
use App\Infrastructure\Persistence\Postgres\PdoCardRepository;
use App\Infrastructure\Persistence\Postgres\PdoConnectionFactory;
use App\Infrastructure\Persistence\Postgres\PdoTransactionRepository;
use App\Infrastructure\Persistence\Postgres\PdoUnitOfWork;

final class Kernel
{
    /**
     * @param array<string, string> $headers
     */
    public function handle(string $method, string $path, string $rawBody, array $headers = []): Response
    {
        if ($method === 'GET' && $path === '/health') {
            return new Response(200, ['status' => 'ok']);
        }

        if ($method === 'POST' && $path === '/transactions/authorize') {
            return $this->authorizeTransaction($rawBody, $headers);
        }

        return new Response(404, ['error' => 'not_found']);
    }

    /**
     * @param array<string, string> $headers
     */
    private function authorizeTransaction(string $rawBody, array $headers): Response
    {
        try {
            $signingSecret = getenv('SIGNING_SECRET');
            if (!is_string($signingSecret) || $signingSecret === '') {
                return new Response(503, ['error' => 'service_unavailable']);
            }

            $signature = $this->getHeader($headers, 'X-Signature');
            try {
                (new HmacSignatureValidator($signingSecret))->validate($rawBody, $signature);
            } catch (\RuntimeException) {
                return new Response(401, ['error' => 'invalid_signature']);
            }

            $data = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                return new Response(400, ['error' => 'invalid_json']);
            }

            foreach (['card_id', 'external_transaction_id', 'amount', 'vendor_id'] as $key) {
                if (!array_key_exists($key, $data)) {
                    return new Response(422, ['error' => 'validation_failed', 'field' => $key]);
                }
            }

            $databaseUrl = getenv('DATABASE_URL');
            if (!is_string($databaseUrl) || $databaseUrl === '') {
                return new Response(503, ['error' => 'service_unavailable']);
            }

            $cfg = DatabaseUrl::parse($databaseUrl);
            $pdo = PdoConnectionFactory::createFromDsn($cfg->dsn, $cfg->user, $cfg->password);

            $uow = new PdoUnitOfWork($pdo);
            $cards = new PdoCardRepository($pdo);
            $accounts = new PdoBankAccountRepository($pdo);
            $transactions = new PdoTransactionRepository($pdo);

            $handler = new AuthorizeTransactionHandler($uow, $cards, $accounts, $transactions);
            $controller = new TransactionController($handler);

            $request = new AuthorizeTransactionRequest(
                card_id: (int) $data['card_id'],
                external_transaction_id: (string) $data['external_transaction_id'],
                amount: (string) $data['amount'],
                vendor_id: (string) $data['vendor_id'],
            );

            $resource = $controller->authorize($request);

            return new Response(200, $resource->toArray());
        } catch (TransactionException $e) {
            return new Response($e->statusCode, ['error' => $e->reason->value]);
        } catch (\JsonException) {
            return new Response(400, ['error' => 'invalid_json']);
        } catch (\Throwable) {
            return new Response(500, ['error' => 'internal_error']);
        }
    }

    /**
     * @param array<string, string> $headers
     */
    private function getHeader(array $headers, string $name): string
    {
        $needle = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $needle) {
                return $value;
            }
        }

        return '';
    }
}

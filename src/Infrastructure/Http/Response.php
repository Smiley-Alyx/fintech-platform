<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

final class Response
{
    /**
     * @param array<string, mixed> $body
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly array $body,
        public readonly array $headers = ['Content-Type' => 'application/json'],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function ok(array $data, int $statusCode = 200): self
    {
        return new self($statusCode, ['data' => $data]);
    }

    /**
     * @param array<string, mixed> $details
     */
    public static function error(string $code, int $statusCode, array $details = []): self
    {
        $payload = ['error' => ['code' => $code]];
        if ($details !== []) {
            $payload['error']['details'] = $details;
        }

        return new self($statusCode, $payload);
    }
}

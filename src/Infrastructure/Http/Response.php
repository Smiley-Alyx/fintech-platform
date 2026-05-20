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
}

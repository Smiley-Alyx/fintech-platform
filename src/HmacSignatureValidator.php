<?php

declare(strict_types=1);

namespace App;

class HmacSignatureValidator
{
    public function __construct(
        private string $secret,
    ) {
    }

    public function validate(string $body, string $signature): void
    {
        if ($signature === '') {
            throw new \RuntimeException('Signature header missing');
        }

        $expected = hash_hmac('sha256', $body, $this->secret);

        if (!hash_equals($expected, $signature)) {
            throw new \RuntimeException('Signature mismatch');
        }
    }
}

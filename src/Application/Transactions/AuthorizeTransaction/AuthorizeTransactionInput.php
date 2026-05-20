<?php

declare(strict_types=1);

namespace App\Application\Transactions\AuthorizeTransaction;

final class AuthorizeTransactionInput
{
    public function __construct(
        public readonly int $cardId,
        public readonly string $externalTransactionId,
        public readonly string $amount,
        public readonly string $vendorId,
    ) {
    }
}

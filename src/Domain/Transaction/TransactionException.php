<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use RuntimeException;

class TransactionException extends RuntimeException
{
    public function __construct(
        public readonly TransactionErrorReason $reason,
        public readonly int $statusCode = 400,
    ) {
        parent::__construct($reason->value, $statusCode);
    }
}

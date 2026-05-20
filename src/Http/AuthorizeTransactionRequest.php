<?php

declare(strict_types=1);

namespace App\Http;

class AuthorizeTransactionRequest
{
    public function __construct(
        public readonly int $card_id,
        public readonly string $external_transaction_id,
        public readonly string $amount,
        public readonly string $vendor_id,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App;

use App\Domain\Transaction\TransactionErrorReason;

final class ComplianceResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?TransactionErrorReason $errorReason = null,
    ) {
    }

    public static function success(): self
    {
        return new self(true);
    }

    public static function failure(TransactionErrorReason $reason): self
    {
        return new self(false, $reason);
    }

    public function isFailure(): bool
    {
        return !$this->success;
    }
}

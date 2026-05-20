<?php

declare(strict_types=1);

namespace App\Domain\Accounts;

use App\Models\BankAccount;

interface BankAccountRepository
{
    /**
     * @return BankAccount[]
     */
    public function findActiveByCardId(int $cardId): array;

    public function save(BankAccount $account): void;
}

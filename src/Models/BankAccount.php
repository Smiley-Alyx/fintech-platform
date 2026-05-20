<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Transaction\TransactionErrorReason;
use App\Domain\Transaction\TransactionException;

class BankAccount
{
    public int $id;
    public int $card_id;
    /**
     * @var string decimal(18,2)
     */
    public string $balance;
    public AccountStatus $status;

    public function debit(Transaction $transaction): void
    {
        if ($this->status !== AccountStatus::ACTIVE) {
            throw new TransactionException(TransactionErrorReason::ACCOUNT_INACTIVE, 403);
        }

        $amount = $transaction->amount;
        if ($this->compareDecimal($this->balance, $amount) < 0) {
            throw new TransactionException(TransactionErrorReason::INSUFFICIENT_BALANCE, 409);
        }

        $this->balance = $this->subDecimal($this->balance, $amount);
    }

    private function compareDecimal(string $left, string $right): int
    {
        if (function_exists('bccomp')) {
            return bccomp($left, $right, 2);
        }

        return (int) round(((float) $left - (float) $right) * 100);
    }

    private function subDecimal(string $left, string $right): string
    {
        if (function_exists('bcsub')) {
            return bcsub($left, $right, 2);
        }

        return number_format((float) $left - (float) $right, 2, '.', '');
    }
}

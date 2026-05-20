<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Models\Transaction;

interface TransactionRepository
{
    public function create(Transaction $transaction): Transaction;
}

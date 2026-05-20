<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

enum TransactionStatus: string
{
    case AUTHORIZED = 'AUTHORIZED';
    case DECLINED = 'DECLINED';
}

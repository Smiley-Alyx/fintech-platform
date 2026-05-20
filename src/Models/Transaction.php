<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Transaction\TransactionStatus;

class Transaction
{
    public int $id;
    public int $card_id;
    public string $external_transaction_id;
    public int $bank_account_id;
    public string $vendor_id;
    public string $amount;
    public TransactionStatus $status;
    public ?string $decline_reason = null;
    public string $created_at;
    public ?string $updated_at = null;
}

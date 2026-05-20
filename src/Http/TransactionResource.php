<?php

declare(strict_types=1);

namespace App\Http;

use App\Application\Transactions\AuthorizeTransaction\AuthorizeTransactionResult;

class TransactionResource
{
    public function __construct(private AuthorizeTransactionResult $transaction)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'              => $this->transaction->id,
            'card_id'         => $this->transaction->cardId,
            'bank_account_id' => $this->transaction->bankAccountId,
            'vendor_id'       => $this->transaction->vendorId,
            'amount'          => $this->transaction->amount,
            'created_at'      => $this->transaction->createdAt,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Transactions\AuthorizeTransaction;

final class AuthorizeTransactionResult
{
    public function __construct(
        public readonly int $id,
        public readonly int $cardId,
        public readonly int $bankAccountId,
        public readonly string $vendorId,
        public readonly string $amount,
        public readonly string $status,
        public readonly ?string $declineReason,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'card_id' => $this->cardId,
            'bank_account_id' => $this->bankAccountId,
            'vendor_id' => $this->vendorId,
            'amount' => $this->amount,
            'status' => $this->status,
            'decline_reason' => $this->declineReason,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

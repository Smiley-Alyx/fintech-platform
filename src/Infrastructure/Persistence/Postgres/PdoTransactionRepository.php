<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Postgres;

use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionStatus;
use App\Models\Transaction;
use PDO;

final class PdoTransactionRepository implements TransactionRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(Transaction $transaction): Transaction
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO transactions (external_transaction_id, card_id, bank_account_id, vendor_id, amount, status, decline_reason, created_at, updated_at) '
            . 'VALUES (:external_transaction_id, :card_id, :bank_account_id, :vendor_id, :amount, :status, :decline_reason, :created_at, :updated_at) '
            . 'RETURNING id'
        );

        $stmt->execute([
            'external_transaction_id' => $transaction->external_transaction_id,
            'card_id' => $transaction->card_id,
            'bank_account_id' => $transaction->bank_account_id,
            'vendor_id' => $transaction->vendor_id,
            'amount' => $transaction->amount,
            'status' => $transaction->status->value,
            'decline_reason' => $transaction->decline_reason,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ]);

        $id = $stmt->fetchColumn();
        $transaction->id = (int) $id;

        if (!$transaction->status instanceof TransactionStatus) {
            $transaction->status = TransactionStatus::AUTHORIZED;
        }

        return $transaction;
    }
}

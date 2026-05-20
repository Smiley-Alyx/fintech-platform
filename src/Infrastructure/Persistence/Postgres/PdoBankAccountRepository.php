<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Postgres;

use App\Domain\Accounts\BankAccountRepository;
use App\Models\AccountStatus;
use App\Models\BankAccount;
use PDO;

final class PdoBankAccountRepository implements BankAccountRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findActiveByCardId(int $cardId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, card_id, balance, status FROM bank_accounts WHERE card_id = :card_id AND status = :status ORDER BY id ASC'
        );
        $stmt->execute([
            'card_id' => $cardId,
            'status' => AccountStatus::ACTIVE->value,
        ]);

        $rows = $stmt->fetchAll();
        $result = [];

        foreach ($rows as $row) {
            $account = new BankAccount();
            $account->id = (int) $row['id'];
            $account->card_id = (int) $row['card_id'];
            $account->balance = (string) $row['balance'];
            $account->status = AccountStatus::from((string) $row['status']);
            $result[] = $account;
        }

        return $result;
    }

    public function save(BankAccount $account): void
    {
        $stmt = $this->pdo->prepare('UPDATE bank_accounts SET balance = :balance, status = :status WHERE id = :id');
        $stmt->execute([
            'id' => $account->id,
            'balance' => $account->balance,
            'status' => $account->status->value,
        ]);
    }
}

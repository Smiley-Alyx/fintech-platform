<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Postgres;

use App\Domain\Accounts\CardRepository;
use App\Models\Card;
use App\Models\CardStatus;
use PDO;

final class PdoCardRepository implements CardRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?Card
    {
        $stmt = $this->pdo->prepare('SELECT id, external_id, status FROM cards WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        $card = new Card();
        $card->id = (int) $row['id'];
        $card->external_id = (string) $row['external_id'];
        $card->status = CardStatus::from((string) $row['status']);

        return $card;
    }
}

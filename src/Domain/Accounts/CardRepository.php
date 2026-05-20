<?php

declare(strict_types=1);

namespace App\Domain\Accounts;

use App\Models\Card;

interface CardRepository
{
    public function findById(int $id): ?Card;
}

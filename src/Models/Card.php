<?php

declare(strict_types=1);

namespace App\Models;

class Card
{
    public int $id;
    public string $external_id;
    public CardStatus $status;
}

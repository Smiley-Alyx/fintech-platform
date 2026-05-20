<?php

declare(strict_types=1);

namespace App\Models;

enum CardStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';
}

<?php

declare(strict_types=1);

namespace App\Models;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case FROZEN = 'frozen';
}

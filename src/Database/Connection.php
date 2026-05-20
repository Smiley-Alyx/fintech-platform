<?php

declare(strict_types=1);

namespace App\Database;

interface Connection
{
    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}

<?php

declare(strict_types=1);

namespace App\Application\Contracts;

interface UnitOfWork
{
    public function begin(): void;

    public function commit(): void;

    public function rollBack(): void;
}

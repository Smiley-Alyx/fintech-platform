<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Postgres;

use PDO;

final class PdoConnectionFactory
{
    public static function createFromDsn(string $dsn, string $user, string $password): PDO
    {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }
}

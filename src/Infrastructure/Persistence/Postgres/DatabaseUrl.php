<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Postgres;

final class DatabaseUrl
{
    public function __construct(
        public readonly string $dsn,
        public readonly string $user,
        public readonly string $password,
    ) {
    }

    public static function parse(string $databaseUrl): self
    {
        $parts = parse_url($databaseUrl);
        if ($parts === false) {
            throw new \InvalidArgumentException('Invalid DATABASE_URL');
        }

        $scheme = $parts['scheme'] ?? '';
        if ($scheme !== 'postgres' && $scheme !== 'postgresql') {
            throw new \InvalidArgumentException('DATABASE_URL scheme must be postgres or postgresql');
        }

        $host = $parts['host'] ?? 'localhost';
        $port = (int) ($parts['port'] ?? 5432);
        $dbName = isset($parts['path']) ? ltrim((string) $parts['path'], '/') : '';
        if ($dbName === '') {
            throw new \InvalidArgumentException('DATABASE_URL must include database name');
        }

        $user = (string) ($parts['user'] ?? '');
        $password = (string) ($parts['pass'] ?? '');

        $query = [];
        if (isset($parts['query'])) {
            parse_str((string) $parts['query'], $query);
        }

        $sslmode = isset($query['sslmode']) ? (string) $query['sslmode'] : null;

        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $dbName);
        if ($sslmode !== null && $sslmode !== '') {
            $dsn .= ';sslmode=' . $sslmode;
        }

        return new self($dsn, $user, $password);
    }
}

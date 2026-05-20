<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Postgres\DatabaseUrl;
use App\Infrastructure\Persistence\Postgres\PdoConnectionFactory;

require __DIR__ . '/../vendor/autoload.php';

$databaseUrl = getenv('DATABASE_URL');
if (!is_string($databaseUrl) || $databaseUrl === '') {
    fwrite(STDERR, "DATABASE_URL is required\n");
    exit(1);
}

$migrationsDir = __DIR__ . '/../migrations';
if (!is_dir($migrationsDir)) {
    fwrite(STDERR, "Migrations directory not found: {$migrationsDir}\n");
    exit(1);
}

$cfg = DatabaseUrl::parse($databaseUrl);
$pdo = PdoConnectionFactory::createFromDsn($cfg->dsn, $cfg->user, $cfg->password);

$pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (version TEXT PRIMARY KEY, applied_at TIMESTAMPTZ NOT NULL DEFAULT NOW())');

$files = glob($migrationsDir . '/*.sql');
if ($files === false) {
    fwrite(STDERR, "Failed to read migrations directory\n");
    exit(1);
}

sort($files, SORT_STRING);

$pdo->beginTransaction();
try {
    $checkStmt = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE version = :version');
    $insertStmt = $pdo->prepare('INSERT INTO schema_migrations (version) VALUES (:version)');

    foreach ($files as $file) {
        $version = basename($file);

        $checkStmt->execute(['version' => $version]);
        $applied = $checkStmt->fetchColumn();
        if ($applied) {
            continue;
        }

        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new RuntimeException("Unable to read migration file: {$file}");
        }

        $pdo->exec($sql);
        $insertStmt->execute(['version' => $version]);
        fwrite(STDOUT, "Applied {$version}\n");
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

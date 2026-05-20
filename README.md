# Fintech Platform

Backend service skeleton for transaction authorization.

## Requirements

- PHP 8.3+
- Composer
- PostgreSQL

## Configuration

The service uses `DATABASE_URL`.

Example:

```bash
export DATABASE_URL='postgresql://user:pass@localhost:5432/fintech?sslmode=disable'
```

## Database migrations

```bash
php bin/migrate.php
```

## Project structure

- `src/Domain` - domain types and business errors
- `src/Application` - use-cases
- `src/Infrastructure` - persistence/adapters (PDO/PostgreSQL)
- `src/Http` - HTTP DTOs/resources/controllers (framework-agnostic)
- `migrations` - SQL migrations
- `bin` - CLI scripts

## Development server (front controller)

Run built-in PHP server:

```bash
php -S 127.0.0.1:8080 -t public

curl -sS http://127.0.0.1:8080/health
```

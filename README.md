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

export DATABASE_URL='postgresql://user:pass@localhost:5432/fintech?sslmode=disable'
export SIGNING_SECRET='change-me'
php bin/migrate.php

curl -sS -X POST http://127.0.0.1:8080/transactions/authorize \
  -H 'Content-Type: application/json' \
  -H "X-Signature: $(echo -n '{\"card_id\":1,\"external_transaction_id\":\"tx_1\",\"amount\":\"10.00\",\"vendor_id\":\"vendor_1\"}' | openssl dgst -sha256 -hmac 'change-me' -r | cut -d' ' -f1)" \
  -d '{"card_id":1,"external_transaction_id":"tx_1","amount":"10.00","vendor_id":"vendor_1"}'
```

CREATE TABLE cards (
    id BIGSERIAL PRIMARY KEY,
    external_id TEXT NOT NULL,
    status TEXT NOT NULL
);

CREATE TABLE bank_accounts (
    id BIGSERIAL PRIMARY KEY,
    card_id BIGINT NOT NULL REFERENCES cards (id),
    balance NUMERIC(18, 2) NOT NULL,
    status TEXT NOT NULL
);

CREATE TABLE transactions (
    id BIGSERIAL PRIMARY KEY,
    external_transaction_id TEXT NOT NULL UNIQUE,
    card_id BIGINT NOT NULL REFERENCES cards (id),
    bank_account_id BIGINT NOT NULL REFERENCES bank_accounts (id),
    vendor_id TEXT NOT NULL,
    amount NUMERIC(18, 2) NOT NULL,
    status TEXT NOT NULL,
    decline_reason TEXT NULL,
    created_at TIMESTAMPTZ NOT NULL,
    updated_at TIMESTAMPTZ NULL
);

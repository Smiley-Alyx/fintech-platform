<?php

declare(strict_types=1);

namespace App\Application\Transactions\AuthorizeTransaction;

use App\Application\Contracts\UnitOfWork;
use App\Domain\Accounts\BankAccountRepository;
use App\Domain\Accounts\CardRepository;
use App\Domain\Transaction\TransactionErrorReason;
use App\Domain\Transaction\TransactionException;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionStatus;
use App\Models\CardStatus;
use App\Models\Transaction;

final class AuthorizeTransactionHandler
{
    public function __construct(
        private UnitOfWork $uow,
        private CardRepository $cards,
        private BankAccountRepository $accounts,
        private TransactionRepository $transactions,
    ) {
    }

    public function handle(AuthorizeTransactionInput $input): AuthorizeTransactionResult
    {
        $card = $this->cards->findById($input->cardId);
        if ($card === null) {
            throw new TransactionException(TransactionErrorReason::CARD_NOT_FOUND, 404);
        }

        if ($card->status !== CardStatus::ACTIVE) {
            throw new TransactionException(TransactionErrorReason::CARD_INACTIVE, 403);
        }

        $this->uow->begin();
        try {
            $accountList = $this->accounts->findActiveByCardId($input->cardId);
            if ($accountList === []) {
                throw new TransactionException(TransactionErrorReason::NO_AVAILABLE_ACCOUNTS, 409);
            }

            $account = $accountList[0];

            $transaction = new Transaction();
            $transaction->card_id = $input->cardId;
            $transaction->external_transaction_id = $input->externalTransactionId;
            $transaction->bank_account_id = $account->id;
            $transaction->vendor_id = $input->vendorId;
            $transaction->amount = $input->amount;
            $transaction->status = TransactionStatus::AUTHORIZED;
            $transaction->created_at = date('c');

            $transaction = $this->transactions->create($transaction);

            $account->debit($transaction);
            $this->accounts->save($account);

            $this->uow->commit();

            return new AuthorizeTransactionResult(
                id: $transaction->id,
                cardId: $transaction->card_id,
                bankAccountId: $transaction->bank_account_id,
                vendorId: $transaction->vendor_id,
                amount: $transaction->amount,
                status: $transaction->status->value,
                declineReason: $transaction->decline_reason,
                createdAt: $transaction->created_at,
                updatedAt: $transaction->updated_at,
            );
        } catch (\Throwable $e) {
            $this->uow->rollBack();
            throw $e;
        }
    }
}

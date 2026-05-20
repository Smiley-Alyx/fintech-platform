<?php

declare(strict_types=1);

namespace App\Http;

use App\Application\Transactions\AuthorizeTransaction\AuthorizeTransactionCommand;
use App\Application\Transactions\AuthorizeTransaction\AuthorizeTransactionHandler;

class TransactionController
{
    public function __construct(
        private AuthorizeTransactionHandler $authorizeTransaction,
    ) {
    }

    public function authorize(AuthorizeTransactionRequest $request): TransactionResource
    {
        $result = $this->authorizeTransaction->handle(new AuthorizeTransactionCommand(
            cardId: $request->card_id,
            externalTransactionId: $request->external_transaction_id,
            amount: $request->amount,
            vendorId: $request->vendor_id,
        ));

        return new TransactionResource($result);
    }
}

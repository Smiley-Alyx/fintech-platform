<?php

declare(strict_types=1);

namespace App;

use App\Http\AuthorizeTransactionRequest;
use App\ComplianceResult;

class ComplianceValidator
{
    public function validate(AuthorizeTransactionRequest $request): ComplianceResult
    {
        return ComplianceResult::success();
    }
}

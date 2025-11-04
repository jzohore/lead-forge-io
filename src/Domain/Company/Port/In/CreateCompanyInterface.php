<?php

declare(strict_types=1);

namespace App\Domain\Company\Port\In;

use App\Application\Company\DTO\Request\CompanyCreateRequest;
use App\Application\Company\DTO\Response\CompanyCreatedResponse;
use App\Application\Company\DTO\Response\CompanyErrorResponse;

interface CreateCompanyInterface
{
    public function __invoke(CompanyCreateRequest $request): CompanyCreatedResponse|CompanyErrorResponse;
}
